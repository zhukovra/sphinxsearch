<?php 
namespace WNeuteboom\SphinxSearch;

class SphinxSearch
{
    protected $connection;

    protected $total_count;
    protected $time;
    protected $term;
    protected $index;
    protected $order = "";
    protected $exclude = array();
    protected $limit = 100;
    protected $offset = 0;
    protected $page = 0;

    protected $table;
    protected $with = array();

    public function __construct($args = array())
    {
        $this->connection = new \Sphinx\SphinxClient();
        $this->connection->setServer($args['host'], $args['port']);
        $this->connection->setConnectTimeout($args['timeout']);
        $this->connection->setMatchMode(\Sphinx\SphinxClient::SPH_MATCH_ANY);
        $this->connection->setSortMode(\Sphinx\SphinxClient::SPH_SORT_RELEVANCE);
    }

    public function select()
    {
        $this->SetSelect(implode(",", func_get_args()));

        return $this;
    }

    public function index($index)
    {
        $this->index = $index;

        return $this;
    }

    public function search($term)
    {
        $this->term = $term;

        return $this;
    }

    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    public function with($with = array())
    {
        $this->with = $with;

        return $this;
    }

    public function where($field, $value) 
    {
        $this->SetFilter($field, $value);

        return $this;
    }

    public function whereNot($field, $value)
    {
        $this->SetFilter($field, $value, true);

        return $this;
    }

    public function whereFloatRange($field, $from, $to)
    {
        $this->SetFilterFloatRange($field, (float)$from, (float)$to);

        return $this;
    }

    public function orderBy($field, $sort_type)
    {
        $this->SetSortMode($sort_type, $field);

        return $this;
    }

    public function skip($value) 
    {
        $this->offset = $value;
        $this->SetLimits($this->offset, $this->limit);

        return $this;
    }

    public function take($value)
    {
        $this->limit = $value;
        $this->SetLimits($this->offset, $this->limit);

        return $this;
    }

    public function weights($value = array()) 
    {
        $this->SetFieldWeights($value);

        return $this;
    }

    public function get() 
    {
        $this->total_count  = 0;
        $this->time         = 0;

        $result = $this->query($this->term, $this->index);

        if ($result)
        {
            $this->total_count  = $result['total_found'];
            $this->time         = $result['time'];

            if ($result['total'] && isset($result['matches']))
            {
                if (!empty($this->table))
                {
                    // Get results' id's and query the database.
                    $match_ids = array_keys($result['matches']);

                    if ($this->table instanceof \Illuminate\Database\Eloquent\Model)
                    {
                        $result = $this->table->whereIn("id", $match_ids)
                                              ->with($this->with)
                                              ->orderByRaw(\DB::raw("FIELD(id, " . implode(",", $match_ids) . ")"))
                                              ->get();

                        return $result;
                    }
                    else if ($this->table)
                    {
                        $result = \DB::table($this->table)
                                        ->whereIn("id", $match_ids)
                                        ->orderByRaw(\DB::raw("FIELD(id, " . implode(",", $match_ids) . ")"))
                                        ->get();   

                        return $result;
                    }
                }

                return $result['matches'];
            }
        }

        return false;
    }

    public function count()
    {
        return $this->total_count;
    }

    public function runtime()
    {
        return $this->time;
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->connection, $method), $parameters);
    }
}