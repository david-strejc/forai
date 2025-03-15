<?php
//FORAI:F2630;DEF[C2253:Mentions,F10690:beforeSave];IMP[];EXP[C2253,F10690];LANG[php]//


namespace tests\unit\testData\Hooks\testCase2\application\Espo\Modules\Crm\Hooks\Note;

class Mentions extends \Espo\Hooks\Note\Mentions
{
    public static int $order = 9;

    public function beforeSave(\Espo\ORM\Entity $entity, array $options): void
    {

    }

}