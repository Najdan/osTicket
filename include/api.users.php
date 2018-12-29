<?php
include_once INCLUDE_DIR.'class.api.php';
include_once INCLUDE_DIR.'class.user.php';
class UserApiController extends ApiController {

    # Copied from TicketApiController.  Not fully implemented
    function getRequestStructure($format, $data=null) {
        return ["phone", "notes", "name", "email","password", "timezone"];
    }
    # Copied from TicketApiController.  Not implemented
    function validate(&$data, $format, $strict=true) {
        //Add as applicable.
        return true;
    }

    public function create(string $format):Response {
        //see ajax.users.php addUser() and class.api.php for example
        if(!($key=$this->requireApiKey()) || !$key->canAddUser())
            return $this->exerr(401, __('API key not authorized'));
        $params = $this->getRequest($format);
        //Maybe use osTicket validation methods instead?
        $params['phone'] = $params['phone'] ?? null;
        $params=array_intersect_key($params, array_flip(['phone','notes','name','email','timezone','password']));
        if (count($params)!==6) {
            $missing=array_diff(['phone','notes','name','email','timezone','password'], array_keys($params));
            return $this->exerr(400, __('Missing parameters '.implode(', ', $missing)));
        }
        if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->exerr(400, __("Invalid email: $params[email]"));
        }
        if(User::lookup(['emails__address'=>$params['email']])) {
            return $this->exerr(400, __("email $params[email] is already in use"));
        }
        if(!$user=User::fromVars($params)) {
            return $this->exerr(400, __('Unknown user creation error'));
        }
        $errors=[];
        $params=array_merge($params,['username'=>$params['email'],'passwd1'=>$params['password'],'passwd2'=>$params['password'],'timezone'=>$params['timezone']]);
        if(!$user->register($params, $errors)) {
            return $this->exerr(400, __('User added but error attempting to register'));
        }
        $this->response(201, json_encode($user->getUserApiEntity()));
        //$this->response(201, $user->to_json());
    }

    public function delete(string $format, int $uid):Response {
        if(!($key=$this->requireApiKey()) || !$key->canDeleteUser())
            return $this->exerr(401, __('API key not authorized'));
        if(!$user = User::lookup($uid))
            return $this->exerr(400, __("User ID '$uid' does not exist"));
        $user->deleteAllTickets();
        $user->delete();
        $this->response(204, null);
    }
}