<?php
class SystemUsersController extends ModelController 
{
    public $modelName = 'system.users';
    
    public $listFields = array(
        'system.users.user_id',
        'system.users.user_name',
        'system.users.first_name',
        'system.users.last_name',
        'system.roles.role_name'
    );
}