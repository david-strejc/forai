<?php
//FORAI:F2622;DEF[C2245:MockDBResult<ArrayIterator>,F10682:fetchAll];IMP[];EXP[C2245,F10682];LANG[php]//


class MockDBResult extends ArrayIterator
{
    public function fetchAll()
    {
        $arr = array();
        foreach ($this as $value) {
            $arr[] = $value;
        }
        return $arr;
    }
}
