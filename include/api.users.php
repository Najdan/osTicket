<?php
include_once INCLUDE_DIR.'class.api.php';
include_once INCLUDE_DIR.'class.user.php';
class UserApiController extends ApiController {
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
}