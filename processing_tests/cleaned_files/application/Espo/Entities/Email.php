<?php
//FORAI:F290;DEF[C151:Email<Entity>,F640:get,F641:has,F642:_setSubject,F643:getReplyToNameInternal,F644:getReplyToAddressInternal,F645:_setIsRead,F646:isManuallyArchived,F647:addAttachment,F648:hasBodyPlain,F649:getBodyPlainWithoutReplyPart,F650:getBodyPlain,F651:getBodyPlainForSending,F652:getBodyForSending,F653:getInlineAttachmentList,F654:getDateSent,F655:getDeliveryDate,F656:getSubject,F657:setStatus,F658:setSubject,F659:setAttachmentIdList,F660:getBody,F661:setBody,F662:setBodyPlain,F663:isHtml,F664:isRead,F665:setIsHtml,F666:setIsPlain,F667:setFromAddress,F668:setToAddressList,F669:setCcAddressList,F670:setBccAddressList,F671:setReplyToAddressList,F672:addToAddress,F673:addCcAddress,F674:addBccAddress,F675:addReplyToAddress,F676:getFromString,F677:getFromAddress,F678:getToAddressList,F679:getCcAddressList,F680:getBccAddressList,F681:getReplyToAddressList,F682:setDummyMessageId,F683:getMessageId,F684:getParentType,F685:getParentId,F686:getParent,F687:setAccount,F688:setParent,F689:getStatus,F690:getAccount,F691:getTeams,F692:getUsers,F693:getAssignedUsers,F694:getAssignedUser,F695:getCreatedBy,F696:getSentBy,F697:setSentBy,F698:getGroupFolder,F699:getReplied,F700:getAttachmentIdList,F701:getEmailRepository,F702:setReplied,F703:setRepliedId,F704:setMessageId,F705:setGroupFolder,F706:setGroupFolderId,F707:getGroupStatusFolder,F708:setGroupStatusFolder,F709:setDateSent,F710:setDeliveryDate,F711:setAssignedUserId,F712:addAssignedUserId,F713:addUserId,F714:getUserColumnIsRead,F715:getUserColumnInTrash,F716:getUserColumnFolderId,F717:setUserColumnFolderId,F718:setUserColumnIsRead,F719:setUserColumnInTrash,F720:getUserSkipNotification,F721:setUserSkipNotification,F722:addTeamId,F723:setTeams,F724:getAttachments,F725:getSendAt,F726:setSendAt,F727:getIcsContents,F728:isReplied,F729:setIsReplied];IMP[];EXP[C151,F640,F641,F643,F644,F646,F647,F648,F649,F650,F651,F652,F653,F654,F655,F656,F657,F658,F659,F660,F661,F662,F663,F664,F665,F666,F667,F668,F669,F670,F671,F672,F673,F674,F675,F676,F677,F678,F679,F680,F681,F682,F683,F684,F685,F686,F687,F688,F689,F690,F691,F692,F693,F694,F695,F696,F697,F698,F699,F700,F701,F702,F703,F704,F705,F706,F707,F708,F709,F710,F711,F712,F713,F714,F715,F716,F717,F718,F719,F720,F721,F722,F723,F724,F725,F726,F727,F728,F729];LANG[php]//

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

namespace Espo\Entities;

use Espo\Core\Field\LinkMultiple;
use Espo\Core\Name\Field;
use Espo\Core\Utils\Util;
use Espo\Core\ORM\Entity;
use Espo\Core\Field\DateTime;
use Espo\Core\Field\LinkParent;
use Espo\Core\Field\Link;
use Espo\Modules\Crm\Entities\Account;
use Espo\ORM\Entity as OrmEntity;
use Espo\ORM\EntityCollection;
use Espo\Repositories\Email as EmailRepository;
use Espo\Tools\Email\Util as EmailUtil;

use RuntimeException;
use stdClass;

class Email extends Entity
{
    public const ENTITY_TYPE = 'Email';

    public const STATUS_BEING_IMPORTED = 'Being Imported';
    public const STATUS_ARCHIVED = 'Archived';
    public const STATUS_SENT = 'Sent';
    public const STATUS_SENDING = 'Sending';
    public const STATUS_DRAFT = 'Draft';

    public const RELATIONSHIP_EMAIL_USER = 'EmailUser';
    public const ALIAS_INBOX = 'emailUserInbox';

    public const USERS_COLUMN_IS_READ = 'isRead';
    public const USERS_COLUMN_IN_TRASH = 'inTrash';
    public const USERS_COLUMN_IN_ARCHIVE = 'inArchive';
    public const USERS_COLUMN_FOLDER_ID = 'folderId';
    public const USERS_COLUMN_IS_IMPORTANT = 'isImportant';

    public const GROUP_STATUS_FOLDER_ARCHIVE = 'Archive';
    public const GROUP_STATUS_FOLDER_TRASH = 'Trash';

    public const SAVE_OPTION_IS_BEING_IMPORTED = 'isBeingImported';
    public const SAVE_OPTION_IS_JUST_SENT = 'isJustSent';

    public function get(string $attribute): mixed
    {
        if ($attribute === 'subject') {
            return $this->get(Field::NAME);
        }

        if ($attribute === 'fromName') {
            return EmailUtil::parseFromName($this->get('fromString') ?? '') ?: null;
        }

        if ($attribute === 'fromAddress') {
            return EmailUtil::parseFromAddress($this->get('fromString') ?? '') ?: null;
        }

        if ($attribute === 'replyToName') {
            return $this->getReplyToNameInternal();
        }

        if ($attribute === 'replyToAddress') {
            return $this->getReplyToAddressInternal();
        }

        if ($attribute === 'bodyPlain') {
            return $this->getBodyPlain();
        }

        return parent::get($attribute);
    }

    public function has(string $attribute): bool
    {
        if ($attribute === 'subject') {
            return $this->has(Field::NAME);
        }

        if ($attribute === 'fromName' || $attribute === 'fromAddress') {
            return $this->has('fromString');
        }

        if ($attribute === 'replyToName' || $attribute === 'replyToAddress') {
            return $this->has('replyToString');
        }

        return parent::has($attribute);
    }

    /** @noinspection PhpUnused */
    protected function _setSubject(?string $value): void
    {
        $this->set(Field::NAME, $value);
    }

    private function getReplyToNameInternal(): ?string
    {
        if (!$this->has('replyToString')) {
            return null;
        }

        $string = $this->get('replyToString');

        if (!$string) {
            return null;
        }

        $string = trim(explode(';', $string)[0]);

        return EmailUtil::parseFromName($string);
    }

    private function getReplyToAddressInternal(): ?string
    {
        if (!$this->has('replyToString')) {
            return null;
        }

        $string = $this->get('replyToString');

        if (!$string) {
            return null;
        }

        $string = trim(explode(';', $string)[0]);

        return EmailUtil::parseFromAddress($string);
    }

    /** @noinspection PhpUnused */
    protected function _setIsRead(?bool $value): void
    {
        $this->setInContainer('isRead', $value !== false);

        if ($value === true || $value === false) {
            $this->setInContainer('isUsers', true);

            return;
        }

        $this->setInContainer('isUsers', false);
    }

    /**
     * @deprecated As of v7.4. As the system user ID may be not constant in the future.
     */
    public function isManuallyArchived(): bool
    {
        if ($this->getStatus() !== Email::STATUS_ARCHIVED) {
            return false;
        }

        return true;
    }

    /**
     * @todo Revise.
     * @deprecated
     */
    public function addAttachment(Attachment $attachment): void
    {
        if (!$this->id) {
            return;
        }

        $attachment->set('parentId', $this->id);
        $attachment->set('parentType', Email::ENTITY_TYPE);

        if (!$this->entityManager) {
            throw new RuntimeException();
        }

        $this->entityManager->saveEntity($attachment);
    }

    public function hasBodyPlain(): bool
    {
        return $this->hasInContainer('bodyPlain') && $this->getFromContainer('bodyPlain');
    }

    /**
     * @since 9.0.0
     */
    public function getBodyPlainWithoutReplyPart(): ?string
    {
        $body = $this->getBodyPlain();

        if (!$body) {
            return null;
        }

        return EmailUtil::stripBodyPlainQuotePart($body) ?: null;
    }

    public function getBodyPlain(): ?string
    {
        if ($this->getFromContainer('bodyPlain')) {
            return $this->getFromContainer('bodyPlain');
        }

        /** @var string $body */
        $body = $this->get('body') ?? '';

        $breaks = ["<br />", "<br>", "<br/>", "<br />", "&lt;br /&gt;", "&lt;br/&gt;", "&lt;br&gt;"];

        $body = str_ireplace($breaks, "\r\n", $body);
        $body = strip_tags($body);

        $reList = [
            '&(quot|#34);',
            '&(amp|#38);',
            '&(lt|#60);',
            '&(gt|#62);',
            '&(nbsp|#160);',
            '&(iexcl|#161);',
            '&(cent|#162);',
            '&(pound|#163);',
            '&(copy|#169);',
            '&(reg|#174);',
        ];

        $replaceList = [
            '',
            '&',
            '<',
            '>',
            ' ',
            '¡',
            '¢',
            '£',
            '©',
            '®',
        ];

        foreach ($reList as $i => $re) {
            /** @var string $body */
            $body = mb_ereg_replace($re, $replaceList[$i], $body, 'i');
        }

        if (!$body) {
            return null;
        }

        return $body;
    }

    public function getBodyPlainForSending(): string
    {
        return $this->getBodyPlain() ?? '';
    }

    public function getBodyForSending(): string
    {
        $body = $this->get('body') ?? '';

        if (!empty($body)) {
            $attachmentList = $this->getInlineAttachmentList();

            foreach ($attachmentList as $attachment) {
                $id = $attachment->getId();

                $body = str_replace(
                    "\"?entryPoint=attachment&amp;id=$id\"",
                    "\"cid:$id\"",
                    $body
                );
            }
        }

        return $body;
    }

    /**
     * @return Attachment[]
     */
    public function getInlineAttachmentList(): array
    {
        $idList = [];

        $body = $this->get('body');

        if (empty($body)) {
            return [];
        }

        $matches = [];

        if (!preg_match_all("/\?entryPoint=attachment&amp;id=([^&=\"']+)/", $body, $matches)) {
            return [];
        }

        if (empty($matches[1]) || !is_array($matches[1])) {
            return [];
        }

        $attachmentList = [];

        foreach ($matches[1] as $id) {
            if (in_array($id, $idList)) {
                continue;
            }

            $idList[] = $id;

            if (!$this->entityManager) {
                throw new RuntimeException();
            }

            /** @var Attachment|null $attachment */
            $attachment = $this->entityManager->getEntityById(Attachment::ENTITY_TYPE, $id);

            if ($attachment) {
                $attachmentList[] = $attachment;
            }
        }

        return $attachmentList;
    }

    public function getDateSent(): ?DateTime
    {
        /** @var ?DateTime */
        return $this->getValueObject('dateSent');
    }

    public function getDeliveryDate(): ?DateTime
    {
        /** @var ?DateTime */
        return $this->getValueObject('deliveryDate');
    }

    public function getSubject(): ?string
    {
        return $this->get('subject');
    }

    /**
     * @param Email::STATUS_* $status
     * @noinspection PhpDocSignatureInspection
     */
    public function setStatus(string $status): self
    {
        $this->set('status', $status);

        return $this;
    }

    public function setSubject(?string $subject): self
    {
        $this->set('subject', $subject);

        return $this;
    }

    /**
     * @param string[] $idList
     */
    public function setAttachmentIdList(array $idList): self
    {
        $this->setLinkMultipleIdList('attachments', $idList);

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->get('body');
    }

    public function setBody(?string $body): self
    {
        $this->set('body', $body);

        return $this;
    }

    public function setBodyPlain(?string $bodyPlain): self
    {
        $this->set('bodyPlain', $bodyPlain);

        return $this;
    }

    public function isHtml(): ?bool
    {
        return $this->get('isHtml');
    }

    public function isRead(): ?bool
    {
        return $this->get('isRead');
    }

    public function setIsHtml(bool $isHtml = true): self
    {
        $this->set('isHtml', $isHtml);

        return $this;
    }

    public function setIsPlain(bool $isPlain = true): self
    {
        $this->set('isHtml', !$isPlain);

        return $this;
    }

    public function setFromAddress(?string $address): self
    {
        $this->set('from', $address);

        return $this;
    }

    /**
     * @param string[] $addressList
     */
    public function setToAddressList(array $addressList): self
    {
        $this->set('to', implode(';', $addressList));

        return $this;
    }

    /**
     * @param string[] $addressList
     */
    public function setCcAddressList(array $addressList): self
    {
        $this->set('cc', implode(';', $addressList));

        return $this;
    }

    /**
     * @param string[] $addressList
     * @noinspection PhpUnused
     */
    public function setBccAddressList(array $addressList): self
    {
        $this->set('bcc', implode(';', $addressList));

        return $this;
    }

    /**
     * @param string[] $addressList
     */
    public function setReplyToAddressList(array $addressList): self
    {
        $this->set('replyTo', implode(';', $addressList));

        return $this;
    }

    public function addToAddress(string $address): self
    {
        $list = $this->getToAddressList();

        $list[] = $address;

        $this->set('to', implode(';', $list));

        return $this;
    }

    public function addCcAddress(string $address): self
    {
        $list = $this->getCcAddressList();

        $list[] = $address;

        $this->set('cc', implode(';', $list));

        return $this;
    }

    public function addBccAddress(string $address): self
    {
        $list = $this->getBccAddressList();

        $list[] = $address;

        $this->set('bcc', implode(';', $list));

        return $this;
    }

    public function addReplyToAddress(string $address): self
    {
        $list = $this->getReplyToAddressList();

        $list[] = $address;

        $this->set('replyTo', implode(';', $list));

        return $this;
    }

    public function getFromString(): ?string
    {
        return $this->get('fromString');
    }

    public function getFromAddress(): ?string
    {
        if (!$this->hasInContainer('from') && !$this->isNew()) {
            $this->getEmailRepository()->loadFromField($this);
        }

        return $this->get('from');
    }

    /**
     * @return string[]
     */
    public function getToAddressList(): array
    {
        if (!$this->hasInContainer('to') && !$this->isNew()) {
            $this->getEmailRepository()->loadToField($this);
        }

        $value = $this->get('to');

        if (!$value) {
            return [];
        }

        return explode(';', $value);
    }

    /**
     * @return string[]
     */
    public function getCcAddressList(): array
    {
        if (!$this->hasInContainer('cc') && !$this->isNew()) {
            $this->getEmailRepository()->loadCcField($this);
        }

        $value = $this->get('cc');

        if (!$value) {
            return [];
        }

        return explode(';', $value);
    }

    /**
     * @return string[]
     */
    public function getBccAddressList(): array
    {
        if (!$this->hasInContainer('bcc') && !$this->isNew()) {
            $this->getEmailRepository()->loadBccField($this);
        }

        $value = $this->get('bcc');

        if (!$value) {
            return [];
        }

        return explode(';', $value);
    }

    /**
     * @return string[]
     */
    public function getReplyToAddressList(): array
    {
        if (!$this->hasInContainer('replyTo') && !$this->isNew()) {
            $this->getEmailRepository()->loadReplyToField($this);
        }

        $value = $this->get('replyTo');

        if (!$value) {
            return [];
        }

        return explode(';', $value);
    }

    public function setDummyMessageId(): self
    {
        $this->set('messageId', 'dummy:' . Util::generateId());

        return $this;
    }

    public function getMessageId(): ?string
    {
        return $this->get('messageId');
    }

    public function getParentType(): ?string
    {
        return $this->get('parentType');
    }

    public function getParentId(): ?string
    {
        return $this->get('parentId');
    }

    public function getParent(): ?OrmEntity
    {
        /** @var ?OrmEntity */
        return $this->relations->getOne(Field::PARENT);
    }

    public function setAccount(Link|Account|null $account): self
    {
        return $this->setRelatedLinkOrEntity('account', $account);
    }

    public function setParent(LinkParent|OrmEntity|null $parent): self
    {
        return $this->setRelatedLinkOrEntity(Field::PARENT, $parent);
    }

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getAccount(): ?Account
    {
        /** @var ?Account */
        return $this->relations->getOne('account');
    }

    public function getTeams(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject(Field::TEAMS);
    }

    public function getUsers(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject('users');
    }

    public function getAssignedUsers(): LinkMultiple
    {
        /** @var LinkMultiple */
        return $this->getValueObject(Field::ASSIGNED_USERS);
    }

    public function getAssignedUser(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject(Field::ASSIGNED_USER);
    }

    public function getCreatedBy(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject(Field::CREATED_BY);
    }

    public function getSentBy(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject('sentBy');
    }

    public function setSentBy(Link|User|null $sentBy): self
    {
        return $this->setRelatedLinkOrEntity('sentBy', $sentBy);
    }

    public function getGroupFolder(): ?Link
    {
        /** @var ?Link */
        return $this->getValueObject('groupFolder');
    }

    public function getReplied(): ?Email
    {
        /** @var ?Email */
        return $this->relations->getOne('replied');
    }

    /**
     * @return string[]
     */
    public function getAttachmentIdList(): array
    {
        /** @var string[] */
        return $this->getLinkMultipleIdList('attachments');
    }

    private function getEmailRepository(): EmailRepository
    {
        if (!$this->entityManager) {
            throw new RuntimeException();
        }

        /** @var EmailRepository */
        return $this->entityManager->getRepository(Email::ENTITY_TYPE);
    }

    public function setReplied(?Email $replied): self
    {
        return $this->setRelatedLinkOrEntity('replied', $replied);
    }

    /**
     * @deprecated As of v9.0.0.
     * @todo Remove in v9.2.0.
     */
    public function setRepliedId(?string $repliedId): self
    {
        $this->set('repliedId', $repliedId);

        return $this;
    }

    public function setMessageId(?string $messageId): self
    {
        $this->set('messageId', $messageId);

        return $this;
    }

    public function setGroupFolder(Link|GroupEmailFolder|null $groupFolder): self
    {
        return $this->setRelatedLinkOrEntity('groupFolder', $groupFolder);
    }

    public function setGroupFolderId(?string $groupFolderId): self
    {
        $groupFolder = $groupFolderId ? Link::create($groupFolderId) : null;

        return $this->setGroupFolder($groupFolder);
    }

    public function getGroupStatusFolder(): ?string
    {
        return $this->get('groupStatusFolder');
    }

    public function setGroupStatusFolder(?string $groupStatusFolder): self
    {
        $this->set('groupStatusFolder', $groupStatusFolder);

        return $this;
    }

    public function setDateSent(?DateTime $dateSent): self
    {
        $this->setValueObject('dateSent', $dateSent);

        return $this;
    }

    public function setDeliveryDate(?DateTime $deliveryDate): self
    {
        $this->setValueObject('deliveryDate', $deliveryDate);

        return $this;
    }

    public function setAssignedUserId(?string $assignedUserId): self
    {
        $this->set('assignedUserId', $assignedUserId);

        return $this;
    }

    public function addAssignedUserId(string $assignedUserId): self
    {
        $this->addLinkMultipleId(Field::ASSIGNED_USERS, $assignedUserId);

        return $this;
    }

    public function addUserId(string $userId): self
    {
        $this->addLinkMultipleId('users', $userId);

        return $this;
    }

    public function getUserColumnIsRead(string $userId): ?bool
    {
        return $this->getLinkMultipleColumn('users', self::USERS_COLUMN_IS_READ, $userId);
    }

    public function getUserColumnInTrash(string $userId): ?bool
    {
        return $this->getLinkMultipleColumn('users', self::USERS_COLUMN_IN_TRASH, $userId);
    }

    public function getUserColumnFolderId(string $userId): ?string
    {
        return $this->getLinkMultipleColumn('users', self::USERS_COLUMN_FOLDER_ID, $userId);
    }

    public function setUserColumnFolderId(string $userId, ?string $folderId): self
    {
        $this->setLinkMultipleColumn('users', self::USERS_COLUMN_FOLDER_ID, $userId, $folderId);

        return $this;
    }

    public function setUserColumnIsRead(string $userId, bool $isRead): self
    {
        $this->setLinkMultipleColumn('users', self::USERS_COLUMN_IS_READ, $userId, $isRead);

        return $this;
    }

    public function setUserColumnInTrash(string $userId, bool $inTrash): self
    {
        $this->setLinkMultipleColumn('users', self::USERS_COLUMN_IN_TRASH, $userId, $inTrash);

        return $this;
    }

    public function getUserSkipNotification(string $userId): bool
    {
        /** @var stdClass $map */
        $map = $this->get('skipNotificationMap') ?? (object) [];

        return $map->$userId ?? false;
    }

    public function setUserSkipNotification(string $userId): self
    {
        /** @var stdClass $map */
        $map = $this->get('skipNotificationMap') ?? (object) [];
        $map->$userId = true;
        $this->set('skipNotificationMap', $map);

        return $this;
    }

    public function addTeamId(string $teamId): self
    {
        $this->addLinkMultipleId(Field::TEAMS, $teamId);

        return $this;
    }

    public function setTeams(LinkMultiple $teams): self
    {
        $this->setValueObject(Field::TEAMS, $teams);

        return $this;
    }

    /**
     * @return EntityCollection<Attachment>
     */
    public function getAttachments(): iterable
    {
        /** @var EntityCollection<Attachment> */
        return $this->relations->getMany('attachments');
    }

    public function getSendAt(): ?DateTime
    {
        /** @var ?DateTime */
        return $this->getValueObject('sendAt');
    }

    public function setSendAt(?DateTime $sendAt): self
    {
        $this->setValueObject('sendAt', $sendAt);

        return $this;
    }

    public function getIcsContents(): ?string
    {
        return $this->get('icsContents');
    }

    public function isReplied(): bool
    {
        return (bool) $this->get('isReplied');
    }

    public function setIsReplied(bool $isReplied = true): self
    {
        return $this->set('isReplied', $isReplied);
    }
}
