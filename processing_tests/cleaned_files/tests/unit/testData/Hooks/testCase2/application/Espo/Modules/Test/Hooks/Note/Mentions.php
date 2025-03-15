<?php
//FORAI:F2629;DEF[C2252:Mentions,F10689:beforeSave];IMP[];EXP[C2252,F10689];LANG[php]//


namespace tests\unit\testData\Hooks\testCase2\application\Espo\Modules\Test\Hooks\Note;

class Mentions extends \Espo\Hooks\Note\Mentions
{
    public static int $order = 9;

    public function beforeSave(\Espo\ORM\Entity $entity, array $options): void
    {

    }
}