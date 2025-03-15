<?php
//FORAI:F2624;DEF[C2249:Mentions,F10686:beforeSave];IMP[];EXP[C2249,F10686];LANG[php]//


namespace tests\unit\testData\Hooks\testCase1\application\Espo\Modules\Test\Hooks\Note;

class Mentions extends \Espo\Hooks\Note\Mentions
{
    public static int $order = 9;

    public function beforeSave(\Espo\ORM\Entity $entity, array $options): void
    {

    }
}