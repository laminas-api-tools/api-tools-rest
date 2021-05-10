<?php

namespace Laminas\ApiTools\Rest;

use ArrayObject;
use InvalidArgumentException;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\Hal\Collection as HalCollection;
use Laminas\ApiTools\MvcAuth\Identity\IdentityInterface;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Response;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Mvc\Router\RouteMatch as V2RouteMatch;
use Laminas\Router\RouteMatch;
use Laminas\Stdlib\Parameters;
use Traversable;

/**
 * Base resource class
 *
 * Essentially, simply marshalls arguments and triggers events; it is useless
 * without listeners to do the actual work.
 */
class Resource implements ResourceInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var null|IdentityInterface
     */
    protected $identity;

    /**
     * @var null|InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var null|Parameters
     */
    protected $queryParams;

    /**
     * @var null|RouteMatch|V2RouteMatch
     */
    protected $routeMatch;

    /**
     * @param array $params
     * @return self
     */
    public function setEventParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getEventParams()
    {
        return $this->params;
    }

    /**
     * @param null|IdentityInterface $identity
     * @return self
     */
    public function setIdentity(IdentityInterface $identity = null)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * @return null|IdentityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param null|InputFilterInterface $inputFilter
     * @return self
     */
    public function setInputFilter(InputFilterInterface $inputFilter = null)
    {
        $this->inputFilter = $inputFilter;
        return $this;
    }

    /**
     * @return null|InputFilterInterface
     */
    public function getInputFilter()
    {
        return $this->inputFilter;
    }

    /**
     * @param Parameters $params
     * @return self
     */
    public function setQueryParams(Parameters $params)
    {
        $this->queryParams = $params;
        return $this;
    }

    /**
     * @return null|Parameters
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param RouteMatch|V2RouteMatch $matches
     * @return self
     */
    public function setRouteMatch($matches)
    {
        if (! ($matches instanceof RouteMatch || $matches instanceof V2RouteMatch)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects a %s or %s instance; received %s',
                __METHOD__,
                RouteMatch::class,
                V2RouteMatch::class,
                (is_object($matches) ? get_class($matches) : gettype($matches))
            ));
        }
        $this->routeMatch = $matches;
        return $this;
    }

    /**
     * @return null|RouteMatch|V2RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return self
     */
    public function setEventParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * @param mixed $name
     * @param mixed $default
     * @return mixed
     */
    public function getEventParam($name, $default = null)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        return $default;
    }

    /**
     * Set event manager instance
     *
     * Sets the event manager identifiers to the current class, this class, and
     * the resource interface.
     *
     * @param  EventManagerInterface $events
     * @return self
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->addIdentifiers([
            get_class($this),
            __CLASS__,
            'Laminas\ApiTools\Rest\ResourceInterface',
        ]);
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve event manager
     *
     * Lazy-instantiates an EM instance if none provided.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (! $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Create a record in the resource
     *
     * Expects either an array or object representing the item to create. If
     * a non-array, non-object is provided, raises an exception.
     *
     * The value returned by the last listener to the "create" event will be
     * returned as long as it is an array or object; otherwise, the original
     * $data is returned. If you wish to indicate failure to create, raise a
     * Laminas\ApiTools\Rest\Exception\CreationException from a listener.
     *
     * @param  array|object $data
     * @return array|object
     * @throws Exception\InvalidArgumentException
     */
    public function create($data)
    {
        if (is_array($data)) {
            $data = (object) $data;
        }
        if (! is_object($data)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Data provided to create must be either an array or object; received "%s"',
                gettype($data)
            ));
        }

        $results = $this->triggerEvent(__FUNCTION__, ['data' => $data]);
        $last    = $results->last();
        if (! is_array($last) && ! is_object($last)) {
            return $data;
        }
        return $last;
    }

    /**
     * Update (replace) an existing item
     *
     * Updates the item indicated by $id, replacing it with the information
     * in $data. $data should be a full representation of the item, and should
     * be an array or object; if otherwise, an exception will be raised.
     *
     * Like create(), the return value of the last executed listener will be
     * returned, as long as it is an array or object; otherwise, $data is
     * returned. If you wish to indicate failure to update, raise a
     * Laminas\ApiTools\Rest\Exception\UpdateException.
     *
     * @param  string|int $id
     * @param  array|object $data
     * @return array|object
     * @throws Exception\InvalidArgumentException
     */
    public function update($id, $data)
    {
        if (is_array($data)) {
            $data = (object) $data;
        }
        if (! is_object($data)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Data provided to update must be either an array or object; received "%s"',
                gettype($data)
            ));
        }

        $results = $this->triggerEvent(__FUNCTION__, compact('id', 'data'));
        $last    = $results->last();
        if (! is_array($last) && ! is_object($last)) {
            return $data;
        }
        return $last;
    }

    /**
     * Update (replace) an existing collection of items
     *
     * Replaces the collection with  the items contained in $data.
     * $data should be a multidimensional array or array of objects; if
     * otherwise, an exception will be raised.
     *
     * Like update(), the return value of the last executed listener will be
     * returned, as long as it is an array or object; otherwise, $data is
     * returned. If you wish to indicate failure to update, raise a
     * Laminas\ApiTools\Rest\Exception\UpdateException.
     *
     * @param  array $data
     * @return array|object
     * @throws Exception\InvalidArgumentException
     */
    public function replaceList($data)
    {
        if (! is_array($data)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Data provided to replaceList must be either a multi-dimensional array '
                . 'or array of objects; received "%s"',
                gettype($data)
            ), 400);
        }

        array_walk($data, function ($value, $key) use (&$data) {
            if (is_array($value)) {
                $data[$key] = (object) $value;
                return;
            }

            if (! is_object($value)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Data provided to replaceList must contain only arrays or objects; received "%s"',
                    gettype($value)
                ), 400);
            }
        });

        $results = $this->triggerEvent(__FUNCTION__, ['data' => $data]);
        $last    = $results->last();
        if (! is_array($last) && ! is_object($last)) {
            return $data;
        }
        return $last;
    }

    /**
     * Partial update of an existing item
     *
     * Update the item indicated by $id, using the information from $data;
     * $data should be merged with the existing item in order to provide a
     * partial update. Additionally, $data should be an array or object; any
     * other value will raise an exception.
     *
     * Like create(), the return value of the last executed listener will be
     * returned, as long as it is an array or object; otherwise, $data is
     * returned. If you wish to indicate failure to update, raise a
     * Laminas\ApiTools\Rest\Exception\PatchException.
     *
     * @param  string|int $id
     * @param  array|object $data
     * @return array|object
     * @throws Exception\InvalidArgumentException
     */
    public function patch($id, $data)
    {
        if (is_array($data)) {
            $data = (object) $data;
        }
        if (! is_object($data)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Data provided to patch must be either an array or object; received "%s"',
                gettype($data)
            ));
        }

        $results = $this->triggerEvent(__FUNCTION__, compact('id', 'data'));
        $last    = $results->last();
        if (! is_array($last) && ! is_object($last)) {
            return $data;
        }
        return $last;
    }

    /**
     * Patches the collection with  the items contained in $data.
     * $data should be a multidimensional array or array of objects; if
     * otherwise, an exception will be raised.
     *
     * Like update(), the return value of the last executed listener will be
     * returned, as long as it is an array or object; otherwise, $data is
     * returned.
     *
     * As this method can create and update resources, if you wish to indicate
     * failure to update, raise a PhlyRestfully\Exception\UpdateException and
     * if you wish to indicate a failure to create, raise a
     * PhlyRestfully\Exception\CreationException.
     *
     * @param  array $data
     * @return array|object
     * @throws Exception\InvalidArgumentException
     */
    public function patchList($data)
    {
        if (! is_array($data)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Data provided to patchList must be either a multidimensional array or array of objects; received "%s"',
                gettype($data)
            ), 400);
        }

        $original = $data;
        array_walk($data, function ($value, $key) use (&$data) {
            if (is_array($value)) {
                $data[$key] = new ArrayObject($value);
                return;
            }

            if (! is_object($value)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Data provided to patchList must contain only arrays or objects; received "%s"',
                    gettype($value)
                ), 400);
            }
        });

        $data    = new ArrayObject($data);
        $results = $this->triggerEvent(__FUNCTION__, ['data' => $data]);
        $last    = $results->last();
        if (! is_array($last) && ! is_object($last)) {
            return $original;
        }
        return $last;
    }

    /**
     * Delete an existing item
     *
     * Use to delete the item indicated by $id. The value returned by the last
     * listener will be used, as long as it is a boolean; otherwise, a boolean
     * false will be returned, indicating failure to delete.
     *
     * @param  string|int $id
     * @return bool
     */
    public function delete($id)
    {
        $results = $this->triggerEvent(__FUNCTION__, ['id' => $id]);
        $last    = $results->last();
        if (! is_bool($last)
            && ! $last instanceof ApiProblem
            && ! $last instanceof ApiProblemResponse
            && ! $last instanceof Response
        ) {
            return false;
        }
        return $last;
    }

    /**
     * Delete an existing collection of records
     *
     * @param  null|array $data
     * @return bool
     */
    public function deleteList($data = null)
    {
        if ($data
            && (! is_array($data) && ! $data instanceof Traversable)
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a null argument, or an array/Traversable of items and/or ids; received %s',
                __METHOD__,
                gettype($data)
            ));
        }

        $results = $this->triggerEvent(__FUNCTION__, ['data' => $data]);
        $last    = $results->last();
        if (! is_bool($last)
            && ! $last instanceof ApiProblem
            && ! $last instanceof ApiProblemResponse
            && ! $last instanceof Response
        ) {
            return false;
        }
        return $last;
    }

    /**
     * Fetch an existing item
     *
     * Retrieve an existing item indicated by $id. The value of the last
     * listener will be returned, as long as it is an array or object;
     * otherwise, a boolean false value will be returned, indicating a
     * lookup failure.
     *
     * @param  string|int $id
     * @return false|array|object
     */
    public function fetch($id)
    {
        $results = $this->triggerEvent(__FUNCTION__, ['id' => $id]);
        $last    = $results->last();
        if (! is_array($last) && ! is_object($last)) {
            return false;
        }
        return $last;
    }

    /**
     * Fetch a collection of items
     *
     * Use to retrieve a collection of items. The value of the last
     * listener will be returned, as long as it is an array or Traversable;
     * otherwise, an empty array will be returned.
     *
     * The recommendation is to return a \Laminas\Paginator\Paginator instance,
     * which will allow performing paginated sets, and thus allow the view
     * layer to select the current page based on the query string or route.
     *
     * @return array|Traversable
     */
    public function fetchAll()
    {
        $params  = func_get_args();
        $results = $this->triggerEvent(__FUNCTION__, $params);
        $last    = $results->last();
        if (! is_array($last)
            && ! $last instanceof HalCollection
            && ! $last instanceof ApiProblem
            && ! $last instanceof ApiProblemResponse
            && ! is_object($last)
        ) {
            return [];
        }
        return $last;
    }

    /**
     * @param  string $name
     * @param  array $args
     * @return \Laminas\EventManager\ResponseCollection
     */
    protected function triggerEvent($name, array $args)
    {
        return $this->getEventManager()->triggerEventUntil(function ($result) {
            return ($result instanceof ApiProblem
                || $result instanceof ApiProblemResponse
                || $result instanceof Response
            );
        }, $this->prepareEvent($name, $args));
    }

    /**
     * Prepare event parameters
     *
     * Merges any event parameters set in the resources with arguments passed
     * to a resource method, and passes them to the `prepareArgs` method of the
     * event manager.
     *
     * If an input filter is composed, this, too, is injected into the event.
     *
     * @param  string $name
     * @param  array $args
     * @return ResourceEvent
     */
    protected function prepareEvent($name, array $args)
    {
        $event = new ResourceEvent($name, $this, $this->prepareEventParams($args));
        $event->setIdentity($this->getIdentity());
        $event->setInputFilter($this->getInputFilter());
        $event->setQueryParams($this->getQueryParams());
        $event->setRouteMatch($this->getRouteMatch());

        return $event;
    }

    /**
     * Prepare event parameters
     *
     * Ensures event parameters are created as an array object, allowing them to be modified
     * by listeners and retrieved.
     *
     * @param  array $args
     * @return ArrayObject
     */
    protected function prepareEventParams(array $args)
    {
        $defaultParams = $this->getEventParams();
        $params        = array_merge($defaultParams, $args);
        if (empty($params)) {
            return $params;
        }

        return $this->getEventManager()->prepareArgs($params);
    }
}
