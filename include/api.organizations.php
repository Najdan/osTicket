<?php
include_once INCLUDE_DIR.'class.api.php';
include_once INCLUDE_DIR.'class.organization.php';
class OrganizationApiController extends ApiController {

    # Copied from TicketApiController.  Not fully implemented
    function getRequestStructure($format, $data=null) {
        return ['name','address','phone','website','notes'];
    }
    # Copied from TicketApiController.  Not implemented
    function validate(&$data, $format, $strict=true) {
        //Add as applicable.
        return true;
    }

    public function get(string $format, int $oid):Response {
        if(!($key=$this->requireApiKey()) || !$key->canViewUser())
            return $this->exerr(401, __('API key not authorized'));
        if(!$org = Organization::lookup($oid))
            return $this->exerr(400, __("Organization ID '$oid' does not exist"));
        Http::response(200, $org->to_json(), 'application/json');
        //$this->response(200, json_encode($org->getOrganizationApiEntity()));
    }

    public function create(string $format):Response {
        if(!($key=$this->requireApiKey()) || !$key->canAddOrganization())
            return $this->exerr(401, __('API key not authorized'));

        $params = $this->getRequest($format);
        if (empty($params['name'])) {
            return $this->exerr(400, __('Missing organization name'));
        }
        if(Organization::lookup(['name'=>$params['name']])) {
            return $this->exerr(400, __("Organization name $params[name] is already in use"));
        }
        $params=array_merge(array_fill_keys($this->getRequestStructure($format),null), $params);
        $params=array_intersect_key($params, array_flip($this->getRequestStructure($format)));
        if ($missing=array_diff($this->getRequestStructure($format), array_keys($params))) {
            return $this->exerr(400, __('Missing parameters '.implode(', ', $missing)));
        }

        if(!$org=Organization::fromVars($params)) {
            return $this->exerr(400, __('Unknown organization creation error'));
        }
        Http::response(201, $org->to_json(), 'application/json');
        //$this->response(201, json_encode($org->getOrgApiEntity()));
    }

    public function delete(string $format, int $oid):Response {
        if(!($key=$this->requireApiKey()) || !$key->canDeleteOrganization())
            return $this->exerr(401, __('API key not authorized'));
        // Organization::objects()->filter(['id__in' => [$oid]])
        if(!$orgs = Organization::lookup($oid))
            return $this->exerr(400, __("Organization ID '$oid' does not exist"));
        if(!$orgs->delete()){
            return $this->exerr(500, __('Error deleting organization'));
        }
        $this->response(204, null);
    }
}