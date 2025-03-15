<?php
//FORAI:F2627;DEF[C2250:Mentions,F10687:beforeSave];IMP[];EXP[C2250,F10687];LANG[php]//


namespace tests\unit\testData\Hooks\testCase3\application\Espo\Hooks\Note;

class Mentions
{
    public static $order = 9;

    public function beforeSave(\Espo\ORM\Entity $entity, array $options): void
    {

    }
}
