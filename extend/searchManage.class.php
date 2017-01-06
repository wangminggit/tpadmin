<?php

class searchManage {

    public function normalizeText($string) {

        $out = $this->wordSegmentation($string);

        $out = preg_replace_callback(
                "/([\\xc0-\\xff][\\x80-\\xbf]*)/", array($this, 'stripForSearchCallback'), $out);

        $sql = "SHOW GLOBAL VARIABLES LIKE 'ft\\_min\\_word\\_len'";
        
        $connection = Propel::getConnection();
        $connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                
        $statement = $connection->prepare($sql);
        $statement->execute();
        $minLength_objs = $statement->fetchAll();
        $minLength_obj = $minLength_objs[0];
        if ($minLength_obj && $minLength_obj['Variable_name'] == 'ft_min_word_len') {
            $minLength = intval($minLength_obj['Value']);
        } else {
            $minLength = 0;
        }
        if ($minLength > 1) {
            $n = $minLength - 1;
            $out = preg_replace(
                    "/\b(\w{1,$n})\b/", "$1u800", $out);

            $out = preg_replace(
                    "/(\w)\.(\w|\*)/u", "$1u82e$2", $out);
            return $out;
        }
    }

    public function wordSegmentation($string) {
        $reg = "/([\\xc0-\\xff][\\x80-\\xbf]*)/";
        $string = preg_replace($reg, " $1 ", $string);
        $string = preg_replace('/ +/', ' ', $string);
        return $string;
    }

    protected function stripForSearchCallback($matches) {
        return 'u8' . bin2hex($matches[1]);
    }

    /**
     * Parse the user's query and transform it into an SQL fragment which will 
     * become part of a WHERE clause
     */
    public function parseQuery($filteredText, $field = 'title') {
        $lc = "A-Za-z_'.0-9\\x80-\\xFF\\-"; // Minus format chars
        $searchon = '';

        # FIXME: This doesn't handle parenthetical expressions.
        $m = array();
        if (preg_match_all('/([-+<>~]?)(([' . $lc . ']+)(\*?)|"[^"]*")/', $filteredText, $m, PREG_SET_ORDER)) {
            foreach ($m as $bits) {
                @list( /* all */, $modifier, $term, $nonQuoted, $wildcard ) = $bits;

                if ($nonQuoted != '') {
                    $term = $nonQuoted;
                    $quote = '';
                } else {
                    $term = str_replace('"', '', $term);
                    $quote = '"';
                }

                if ($searchon !== '')
                    $searchon .= ' ';
                if (($modifier == '')) {
                    // If we leave this out, boolean op defaults to OR which is rarely helpful.
                    $modifier = '+';
                }

                // Some languages such as Serbian store the input form in the search index,
                // so we may need to search for matches in multiple writing system variants.
                $convertedVariants = $term;
                if (is_array($convertedVariants)) {
                    $variants = array_unique(array_values($convertedVariants));
                } else {
                    $variants = array($term);
                }

                // The low-level search index does some processing on input to work
                // around problems with minimum lengths and encoding in MySQL's
                // fulltext engine.
                // For Chinese this also inserts spaces between adjacent Han characters.
                $strippedVariants = $variants;

                // Some languages such as Chinese force all variants to a canonical
                // form when stripping to the low-level search index, so to be sure
                // let's check our variants list for unique items after stripping.
                $strippedVariants = array_unique($strippedVariants);

                $searchon .= $modifier;
                if (count($strippedVariants) > 1)
                    $searchon .= '(';
                foreach ($strippedVariants as $stripped) {
                    $stripped = $this->normalizeText($stripped);
                    if ($nonQuoted && strpos($stripped, ' ') !== false) {
                        // Hack for Chinese: we need to toss in quotes for
                        // multiple-character phrases since normalizeForSearch()
                        // added spaces between them to make word breaks.
                        $stripped = '"' . trim($stripped) . '"';
                    }
                    $searchon .= "$quote$stripped$quote$wildcard ";
                }
                if (count($strippedVariants) > 1)
                    $searchon .= ')';

                // Match individual terms or quoted phrase in result highlighting...
                // Note that variants will be introduced in a later stage for highlighting!
            }
        } else {
            //Can't understand search query
        }
        $searchon = mysql_real_escape_string($searchon);
        return " MATCH(`searchindex`.$field) AGAINST('$searchon' IN BOOLEAN MODE) ";
    }

    public function queryMain($filteredText, $offset, $limit, $field = 'title', $sort = 'ASC') {
        $match = $this->parseQuery($filteredText, $field);
        $result = 'SELECT `page_id` , `language` FROM `page`, `searchindex` WHERE `page`.id=`searchindex`.page_id AND `page`.is_published=1 AND ' . $match .
                'ORDER BY `page`.created_at '.$sort.' limit ' . $offset . ' , ' . $limit . '';
        return $result;
    }

    public function queryCountMain($filteredText, $field = 'title') {
        $match = $this->parseQuery($filteredText, $field);
        $result = 'SELECT `page_id` , `language` FROM `page`, `searchindex` WHERE `page`.id=`searchindex`.page_id AND `page`.is_published=1 AND ' . $match;
        return $result;
    }

}

?>
