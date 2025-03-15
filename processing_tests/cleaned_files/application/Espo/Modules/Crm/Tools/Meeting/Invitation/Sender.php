<?php
//FORAI:F2423;DEF[C2033:Sender,F10223:__construct,F10224:sendInvitation,F10225:sendCancellation,F10226:sendInternal,F10227:findTarget,F10228:getSender,F10229:checkStatus,F10230:getCanceledStatusList];IMP[F1989:C1619,F926:C705,F846:C649,F1437:C1185,F1909:C1608,F1662:C1385,F1665:C1390,F2393:C2005,F468:C290];EXP[C2033,F10224,F10225,F10226,F10227,F10228,F10229,F10230];LANG[php]//

/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM – Open Source CRM application.
 * Copyright (C) 2014-2025 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Modules\Crm\Tools\Meeting\Invitation;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\InjectableFactory;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\Name\Field;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;
use Espo\Modules\Crm\Business\Event\Invitations;
use Espo\Modules\Crm\Entities\Call;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Tools\Email\SendService;

/**
 * @since 9.0.0
 */
class Sender
{
    private const TYPE_INVITATION = 'invitation';
    private const TYPE_CANCELLATION = 'cancellation';

    public function __construct(
        private SendService $sendService,
        private User $user,
        private InjectableFactory $injectableFactory,
        private EntityManager $entityManager,
        private Config $config,
        private Metadata $metadata,
    ) {}

    /**
     * @param Meeting|Call $entity
     * @param ?Invitee[] $targets
     * @return Entity[] Entities an invitation was sent to.
     * @throws SendingError
     * @throws Forbidden
     */
    public function sendInvitation(Meeting|Call $entity, ?array $targets = null): array
    {
        return $this->sendInternal($entity, self::TYPE_INVITATION, $targets);
    }

    /**
     * @param Meeting|Call $entity
     * @param ?Invitee[] $targets
     * @return Entity[] Entities an invitation was sent to.
     * @throws SendingError
     * @throws Forbidden
     */
    public function sendCancellation(Meeting|Call $entity, ?array $targets = null): array
    {
        return $this->sendInternal($entity, self::TYPE_CANCELLATION, $targets);
    }


    /**
     * @param ?Invitee[] $targets
     * @return Entity[]
     * @throws SendingError
     * @throws Forbidden
     */
    private function sendInternal(Meeting|Call $entity, string $type, ?array $targets): array
    {
        $this->checkStatus($entity, $type);

        $linkList = [
            Meeting::LINK_USERS,
            Meeting::LINK_CONTACTS,
            Meeting::LINK_LEADS,
        ];

        $sender = $this->getSender();

        $sentAddressList = [];
        $resultEntityList = [];

        foreach ($linkList as $link) {
            $builder = $this->entityManager->getRelation($entity, $link);

            if ($targets === null && $type === self::TYPE_INVITATION) {
                $builder->where(['@relation.status=' => Meeting::ATTENDEE_STATUS_NONE]);
            }

            $collection = $builder->find();

            foreach ($collection as $attendee) {
                $emailAddress = $attendee->get(Field::EMAIL_ADDRESS);

                if ($targets) {
                    $target = self::findTarget($attendee, $targets);

                    if (!$target) {
                        continue;
                    }

                    if ($target->getEmailAddress()) {
                        $emailAddress = $target->getEmailAddress();
                    }
                }

                if (!$emailAddress || in_array($emailAddress, $sentAddressList)) {
                    continue;
                }

                if ($type === self::TYPE_INVITATION) {
                    $sender->sendInvitation($entity, $attendee, $link, $emailAddress);
                }

                if ($type === self::TYPE_CANCELLATION) {
                    $sender->sendCancellation($entity, $attendee, $link, $emailAddress);
                }

                $sentAddressList[] = $emailAddress;
                $resultEntityList[] = $attendee;

                $this->entityManager
                    ->getRelation($entity, $link)
                    ->updateColumns($attendee, ['status' => Meeting::ATTENDEE_STATUS_NONE]);
            }
        }

        return $resultEntityList;
    }

    /**
     * @param Invitee[] $targets
     */
    private static function findTarget(Entity $entity, array $targets): ?Invitee
    {
        foreach ($targets as $target) {
            if (
                $entity->getEntityType() === $target->getEntityType() &&
                $entity->getId() === $target->getId()
            ) {
                return $target;
            }
        }

        return null;
    }

    private function getSender(): Invitations
    {
        $smtpParams = !$this->config->get('eventInvitationForceSystemSmtp') ?
            $this->sendService->getUserSmtpParams($this->user->getId()) :
            null;

        $builder = BindingContainerBuilder::create();

        if ($smtpParams) {
            $builder->bindInstance(SmtpParams::class, $smtpParams);
        }

        return $this->injectableFactory->createWithBinding(Invitations::class, $builder->build());
    }

    /**
     * @throws Forbidden
     */
    private function checkStatus(Meeting|Call $entity, string $type): void
    {
        $entityType = $entity->getEntityType();

        if ($type === self::TYPE_CANCELLATION) {
            if (!in_array($entity->getStatus(), $this->getCanceledStatusList($entityType))) {
                throw new Forbidden("Can't send invitation for not canceled event.");
            }

            return;
        }

        $notActualStatusList = [
            ...($this->metadata->get("scopes.$entityType.completedStatusList") ?? []),
            ...$this->getCanceledStatusList($entityType),
        ];

        if (in_array($entity->getStatus(), $notActualStatusList)) {
            throw new Forbidden("Can't send invitation for not actual event.");
        }
    }

    /**
     * @return string[]
     */
    private function getCanceledStatusList(string $entityType): array
    {
        return $this->metadata->get("scopes.$entityType.canceledStatusList") ?? [];
    }
}
