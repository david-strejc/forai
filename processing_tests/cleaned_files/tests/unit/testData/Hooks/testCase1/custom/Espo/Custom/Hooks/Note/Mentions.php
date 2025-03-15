<?php
//FORAI:F2626;DEF[C2247:Mentions,F10684:beforeSave];IMP[];EXP[C2247,F10684];LANG[php]//


namespace tests\unit\testData\Hooks\testCase1\custom\Espo\Custom\Hooks\Note;

class Mentions extends \Espo\Hooks\Note\Mentions
{
    public static int $order = 7;

    public function beforeSave(\Espo\ORM\Entity $entity, array $options): void
    {

    }
}