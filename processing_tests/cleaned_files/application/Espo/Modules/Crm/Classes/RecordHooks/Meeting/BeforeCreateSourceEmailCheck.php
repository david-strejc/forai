<?php
//FORAI:F2589;DEF[C2197:BeforeCreateSourceEmailCheck,F10631:__construct,F10632:process];IMP[F926:C705,F1802:C1518];EXP[C2197,F10632];LANG[php]//


namespace Espo\Modules\Crm\Classes\RecordHooks\Meeting;

use Espo\Core\Acl;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Record\CreateParams;
use Espo\Core\Record\Hook\CreateHook;
use Espo\Entities\Email;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

/**
 * @implements CreateHook<Meeting>
 */
class BeforeCreateSourceEmailCheck implements CreateHook
{
    public function __construct(
        private EntityManager $entityManager,
        private Acl $acl,
    ) {}

    public function process(Entity $entity, CreateParams $params): void
    {
        $emailId = $entity->get('sourceEmailId');

        if (!$emailId) {
            return;
        }

        $email = $this->entityManager->getRDBRepositoryByClass(Email::class)->getById($emailId);

        if (!$email) {
            return;
        }

        if (!$this->acl->checkEntityRead($email)) {
            throw new Forbidden("No access to source email.");
        }
    }
}
