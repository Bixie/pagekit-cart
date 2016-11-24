<?php

namespace Bixie\Cart\Calculation\Prop;


trait PropModelTrait {

    /**
     * @Column(type="json_array")
     * @var Prop[]
     */
    public $props;

    /**
     * @var PropValueCollection
     */
    protected $propValues;

    /**
     * @return bool
     */
    public function hasProps () {
        return count($this->props) > 0;
    }

    /**
     * @return PropValueCollection
     */
    public function getPropValues () {
        if (!$this->propValues) {
            $this->propValues = new PropValueCollection($this->get('propvalues', []));
        }
        return $this->propValues;
    }

    /**
     * @param PropValueCollection $propValues
     */
    public function setPropValues ($propValues) {
        $this->propValues = $propValues;
    }



    /**
     * @return Prop[]
     */
    public function getProps () {
        $props = array_map(function ($prop) {
            return $prop instanceof Prop ? $prop : new Prop($prop);
        }, ($this->props ?: []));
        usort($props, function ($a, $b) {
            if ($a->ordering == $b->ordering) {
                return 0;
            }
            return ($a->ordering < $b->ordering) ? -1 : 1;
        });
        return $this->props = $props;
    }

    /**
     * @return PropValueCollection[]
     */
    public function getVarieties () {
        $varieties = [];
        $propValues = new PropValueCollection();
        $i = 0;
        foreach ($this->getProps() as $prop) {
            if (empty($prop->config['price_prop'])) continue;
            if ($i == 0) {
                foreach ($prop->options as $option) {
                    $propValues->add((new PropValue(['option' => $option, 'prop' => $prop])));
                    $varieties[] = clone $propValues;
                }
            } else {
                $varieties = $this->_getVarieties($varieties, $prop);
            }
            $i++;
        }
        if (!count($varieties)) {
            $varieties[] = $propValues; //default empty variety
        }
        return $varieties;
    }

    /**
     * @param PropValueCollection[] $varieties
     * @param $prop
     * @return array
     */
    protected function _getVarieties ($varieties, $prop) {
        $adds = [];
        foreach ($varieties as $propValues) {
            foreach ($prop->options as $option) {
                $new = clone $propValues;
                $new->add((new PropValue(['option' => $option, 'prop' => $prop])));
                $adds[] = $new;
            }
        }
        return $adds;
    }



}