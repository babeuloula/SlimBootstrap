<?php

    namespace Core;

    class Truncate {

        public function __construct() {}

        /**
         * Truncates text.
         *
         * Cuts a string to the length of $length and replaces the last characters
         * with the ending if the text is longer than length.
         *
         * @param string  $text String to truncate.
         * @param integer $length Length of returned string, including ellipsis.
         * @param mixed $ending If string, will be used as Ending and appended to the trimmed string. Can also be an associative array that can contain the last three params of this method.
         * @param boolean $exact If false, $text will not be cut mid-word
         * @param boolean $considerHtml If true, HTML tags would be handled correctly
         * @return string Trimmed string.
         **/
        public static function truncate($text, $length = 250, $ending = ' ...', $exact = false, $considerHtml = true) {
            if (is_array($ending)) {
                extract($ending);
            }
            if ($considerHtml) {
                if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                    return $text;
                }
                $totalLength = mb_strlen($ending);
                $openTags = array();
                $truncate = '';
                preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
                foreach ($tags as $tag) {
                    if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                        if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                            array_unshift($openTags, $tag[2]);
                        } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                            $pos = array_search($closeTag[1], $openTags);
                            if ($pos !== false) {
                                array_splice($openTags, $pos, 1);
                            }
                        }
                    }
                    $truncate .= $tag[1];

                    $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                    if ($contentLength + $totalLength > $length) {
                        $left = $length - $totalLength;
                        $entitiesLength = 0;
                        if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                            foreach ($entities[0] as $entity) {
                                if ($entity[1] + 1 - $entitiesLength <= $left) {
                                    $left--;
                                    $entitiesLength += mb_strlen($entity[0]);
                                } else {
                                    break;
                                }
                            }
                        }

                        $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                        break;
                    } else {
                        $truncate .= $tag[3];
                        $totalLength += $contentLength;
                    }
                    if ($totalLength >= $length) {
                        break;
                    }
                }

            } else {
                if (mb_strlen($text) <= $length) {
                    return $text;
                } else {
                    $truncate = mb_substr($text, 0, $length - strlen($ending));
                }
            }
            if (!$exact) {
                $spacepos = mb_strrpos($truncate, ' ');
                if (isset($spacepos)) {
                    if ($considerHtml) {
                        $bits = mb_substr($truncate, $spacepos);
                        preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                        if (!empty($droppedTags)) {
                            foreach ($droppedTags as $closingTag) {
                                if (!in_array($closingTag[1], $openTags)) {
                                    array_unshift($openTags, $closingTag[1]);
                                }
                            }
                        }
                    }
                    $truncate = mb_substr($truncate, 0, $spacepos);
                }
            }

            $truncate .= $ending;

            if ($considerHtml) {
                foreach ($openTags as $tag) {
                    $truncate .= '</'.$tag.'>';
                }
            }

            return $truncate;
        }
    }

?>