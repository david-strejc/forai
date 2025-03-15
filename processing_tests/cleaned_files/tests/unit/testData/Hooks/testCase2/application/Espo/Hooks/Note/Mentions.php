<?php
//FORAI:F2628;DEF[C2251:Mentions,F10688:beforeSave];IMP[];EXP[C2251,F10688];LANG[php]//


namespace tests\unit\testData\Hooks\testCase2\application\Espo\Hooks\Note;

class Mentions
{
    public static $order = 9;

    public function beforeSave(\Espo\ORM\Entity $entity, array $options): void
    {

    }
}
