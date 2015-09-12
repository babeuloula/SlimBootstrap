<?php


    namespace Core;


    /**
     * Class Imbricate
     * @package Core
     */
    class Imbricate {


        /**
         * @param $objects
         *
         * @return mixed
         */
        public static function imbricate($objects) {
            $objects_by_id = array();

            foreach($objects as $object) {
                $id = key($object);

                $objects_by_id[$object->$id] = $object;
            }

            foreach($objects as $key => $object) {
                if($object->parent_id != 0) {
                    $objects_by_id[$object->parent_id]->children[] = $object;
                    unset($objects[$key]);
                }
            }

            return $objects;
        }

    }