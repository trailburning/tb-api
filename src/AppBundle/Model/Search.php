<?php

namespace AppBundle\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 */
class Search
{

    /**
     * @var string
     */
    private $q;

    /**
     * @var DateTime
     */
    private $dateFrom;

    /**
     * @var DateTime
     */
    private $dateTo;

    /**
     * @var string
     */
    private $distanceFrom;

    /**
     * @var string
     */
    private $distanceTo;

    /**
     * @var Point
     */
    private $coords;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    private $distance;

    /**
     * @var string
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\RaceEventType")
     */
    private $type;

    /**
     * @var string
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\RaceCategory")
     */
    private $category;

    /**
     * @var string
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\SearchSort")
     */
    private $sort;

    /**
     * @var string
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\SearchOrder")
     */
    private $order;

    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var array
     */
    private $attributes;

    /**
     * ################################################################################################################.
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */

    /**
     * @return array
     */
    public function getCoordsAsAsocArray()
    {
        return [
            'lat' => $this->coords->getLatitude(),
            'lon' => $this->coords->getLongitude(),
        ];
    }

    /**
     * ################################################################################################################.
     *
     *                                         Getters and Setters
     *
     * ################################################################################################################
     */

    /**
     * Get the value of q.
     *
     * @return string
     */
    public function getQ()
    {
        return $this->q;
    }

    /**
     * Set the value of q.
     *
     * @param string
     *
     * @return Search
     */
    public function setQ($q)
    {
        $this->q = $q;

        return $this;
    }

    /**
     * Get the value of Date From.
     *
     * @return DateTime|null
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set the value of Date From.
     *
     * @param DateTime|null $dateFrom
     *
     * @return Search
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get the value of Date To.
     *
     * @return DateTime|null
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set the value of Date To.
     *
     * @param DateTime|null $dateTo
     *
     * @return Search
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get the value of Coords.
     *
     * @return Point
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set the value of Coords.
     *
     * @param Point $coords
     *
     * @return Search
     */
    public function setCoords(Point $coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get the value of Distance.
     *
     * @return int
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set the value of Distance.
     *
     * @param int $distance
     *
     * @return Search
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get the value of Type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Type.
     *
     * @param string $type
     *
     * @return Search
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of Category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of Category.
     *
     * @param string $category
     *
     * @return Search
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of Sort.
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set the value of Sort.
     *
     * @param string $sort
     *
     * @return Search
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get the value of Order.
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the value of Order.
     *
     * @param string $order
     *
     * @return Search
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the value of Distance From.
     *
     * @return string
     */
    public function getDistanceFrom()
    {
        return $this->distanceFrom;
    }

    /**
     * Set the value of Distance From.
     *
     * @param string $distanceFrom
     *
     * @return Search
     */
    public function setDistanceFrom($distanceFrom)
    {
        $this->distanceFrom = $distanceFrom;

        return $this;
    }

    /**
     * Get the value of Distance To.
     *
     * @return string
     */
    public function getDistanceTo()
    {
        return $this->distanceTo;
    }

    /**
     * Set the value of Distance To.
     *
     * @param string $distanceTo
     *
     * @return Search
     */
    public function setDistanceTo($distanceTo)
    {
        $this->distanceTo = $distanceTo;

        return $this;
    }

    /**
     * Get the value of Limit.
     *
     * @return int
     */
    public function getLimit()
    {
        if ($this->limit === null) {
            return 10;
        }

        return $this->limit;
    }

    /**
     * Set the value of Limit.
     *
     * @param int $limit
     *
     * @return Search
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of Offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the value of Offset.
     *
     * @param int $offset
     *
     * @return Search
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get the value of Attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the value of Attributes.
     *
     * @param array $attributes
     *
     * @return Search
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }
}
