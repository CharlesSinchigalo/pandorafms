<?php

namespace PandoraFMS\Modules\Shared\Services;

use PandoraFMS\Modules\Shared\Exceptions\ForbiddenACLException;
use PandoraFMS\Modules\Users\UserProfiles\Actions\GetUserProfileAction;

class ValidateAclSystem
{


    public function __construct(
        private Config $config,
        private Audit $audit,
        private GetUserProfileAction $getUserProfileAction
    ) {
    }


    public function validate(
        int $idGroup,
        string|array $permissions,
        string $message='',
    ): void {
        // ACL.
        $idUser ??= $this->config->get('id_user');

        $acl = false;
        if (is_array($permissions) === true) {
            foreach ($permissions as $permission) {
                if ((bool) \check_acl($idUser, $idGroup, $permission) === true) {
                    $acl = true;
                }
            }
        } else {
            if ((bool) \check_acl($idUser, $idGroup, $permissions) === true) {
                $acl = true;
            }
        }

        if ($acl === false) {
            $this->audit->write('ACL forbidden user does not have permission ', $message);
            throw new ForbiddenACLException('ACL forbidden user does not have permission '.$message);
        }
    }


    public function validateUserGroups(
        int|array|null $idGroup,
        string $permissions,
        string $message='',
    ): void {
        $idUser ??= $this->config->get('id_user');

        $userGroups = \users_get_groups($idUser, $permissions, false, false);

        $exist = true;
        if (is_array($idGroup) === true) {
            foreach ($idGroup as $group) {
                if (isset($userGroups[$group]) === false) {
                    $exist = false;
                }
            }
        } else {
            if (isset($userGroups[$idGroup]) === false) {
                $exist = false;
            }
        }

        if ($exist === false) {
            $this->audit->write('ACL Forbidden idGroup is not valid for this user', $message);
            throw new ForbiddenACLException('ACL Forbidden idGroup is not valid for this user');
        }
    }


    public function validateUserProfile(
        int $idProfile
    ): void {
        $idUser ??= $this->config->get('id_user');
        $this->getUserProfileAction->__invoke($idUser, $idProfile);
    }


}
