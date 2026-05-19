API
===

Endpoints implemented (from AFNOR specs):

* `createFlow`: `POST /v1/flows`
    * Resource: [CreateFlowResource](src/Flow/ApiPlatform/ApiResource/CreateFlowResource.php)
* `searchFlow`: `POST /v1/flows/searchCompanyBySiren`
    * Resource: [FlowSearchRequestResource](src/Flow/ApiPlatform/ApiResource/FlowSearchRequestResource.php)
* `getFlow`: `GET /v1/flows/{flowId}`
    * Resource: [GetFlowResource](src/Flow/ApiPlatform/ApiResource/GetFlowResource.php)
* `getHealth`: `GET /v1/healthcheck`
    * Resource: [HealthcheckResource](src/Flow/ApiPlatform/ApiResource/HealthcheckResource.php)    
