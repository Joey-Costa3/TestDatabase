<?php
require_once 'class.API.php';

/*
*   CONSTRUCTOR
*   ENDPOINTS
*   HELPERS
*
*/ 

class MyClassScheduler extends API {
    //what to return from endpoint for processing
    //code is http status code.
    private $response = array('code' => 200, 'data' => null, 'message' => '');

    public function __construct($request, $origin) {
        parent::__construct($request);
        //$this->response = array('code' => 200, 'data' => null);        
    }

	//					DEPARTMENTS ENDPOINT <1.0>					//
	/*
			/api/v1/departments?search=
			GET:
				(id: Int) -> RETURN a [department] Limit 1 with the given id or RETURN [] otherwise
				no parameters -> RETURNS all [departments]
				(search: String) -> RETURN [departments] where the search string is in department_short or department_long
				
			POST:
			SPECIFY A POST IN THE CallAPI function!!!!!     /api/v1/departments?short=ArtH&long=Art history
				(id: Int, short: String, long: String) -> update the department at given id RETURN [department] Limit 1
				(short: String, long: String) -> insert the department RETURN [department] LIMIT 1
	*/
	public function departments(){
		if($this->method == 'GET'){
			if(isset($this->request['id'])){ // get a specific department based on id
				$this->response['data'] = getDepartment($this->request['id']);
			}else if(isset($this->request['search'])){
				$this->response['data'] = searchDepartments($this->request['search']);
			}else{ // get all departments
				$this->response['data'] = getDepartments();
			}	
		} else if($this->method == 'POST'){
			if(isset($this->request['short']) && isset($this->request['long'])){
				if(isset($this->request['id'])){// an id was provided -> update
					$this->response['data'] = updateDepartment($this->request['id'],$this->request['short'], $this->request['long']);
				}else{	// an id was not provided -> insert
					$this->response['data'] = insertDepartment($this->request['id'], $this->request['short'], $this->request['long']);
				}
			}else {
				$this->response['message'] = "endpoint requires parameters. Update(id, short, long) || Insert (short, long)";
				$this->response['code'] = 400;
			}
		}else if($this->method == 'DELETE'){
				if(isset($this->request['id'])){
					$this->response['data'] = deleteDepartment($this->request['id']);
				}else{
					$this->response['message'] = "endpoint requires  parametes. Delete(id)";
					$this->response['code'] = 400;
				}
		}else {
			$this->response['message'] = "endpoint does not recognize " . $this->method . " request";
			$this->response['code'] = 405;
		}
		return $this->response;	
	}
	//					PROFESSORS ENDPOINT <1.0>					//
	/*
		/api/v1/professors
		GET:
			(id: Int) -> RETURN a [professor] Limit 1 with the given id or RETURN [] otherwise
			no parameters -> RETURNS all [professors]
			(search: String) -> RETURN [professor] where the given string is contained in either the name or email
		POST:
			(id: Int, name: String, email: String, office: String) -> update the professor at given id RETURN [professor] Limit 1
			(name: String, email: String, office: String) -> insert the professor RETURN [professor] Limit 1
		
	*/
	public function professors(){
		if($this->method == 'GET'){
			if(isset($this->request['id'])){ // get a specific professor based on id
				$this->response['data'] = getProfessor($this->request['id']);
			}else if(isset($this->request['search'])){
				$this->response['data'] = searchProfessors($this->request['search']);
			}else{ // get all professors
				$this->response['data'] = getProfessors();
			}
		}else if($this->method == 'POST'){
			if(isset($this->request['name']) && isset($this->request['email']) && isset($this->request['office'])){
				if(isset($this->request['id'])){ // an id was provided -> update
					$this->response['data'] = updateProfessor($this->request['id'], $this->request['name'], $this->request['email'], $this->request['office']);
				}else{	// an id was not provided -> insert
					$this->response['data'] = insertProfessor($this->request['name'], $this->request['email'], $this->request['office']);
				}
			}else{ // unknown
				$this->response['message'] = "endpoint requires parameters. Update(id, name, email, office) || Insert(name, email, office)";
				$this->response['code'] = 400;
			}
		}else if($this->method == 'DELETE'){
				if(isset($this->request['id'])){
					$this->response['data'] = deleteProfessor($this->request['id']);
				}else{
					$this->response['message'] = "endpoint requires  parametes. Delete(id)";
					$this->response['code'] = 400;
				}
		}else{
			$this->response['message'] = "endpoint does not recognize " . $this->method . " request";
			$this->response['code'] = 405;
		}
		return $this->response;
	}
	
	//					EVENTS ENDPOINT <1.0>					//
	
	/*
		/api/v1/events
		GET: 
			(id: Int) -> RETURN a [event] Limit 1 with the given id or RETURN [] otherwise
			no parameters -> RETURN all [events]
			(search: String) -> RETURN [event] where the given string is contained in the name or description 
		POST:
			(id: Int, start: TimeStamp, end: TimeStamp, name: String, desc: String) -> update the event at the given id RETURN [event] Limit 1
			(start: TimeStamp, end: TimeStamp, name: String, desc: String) -> insert the event RETURN [event] Limit 1
	
	*/
	public function events(){
		if($this->method == 'GET'){
			if(isset($this->request['id'])){ // get a specific event based on id
				$this->response['data'] = getEvent($this->request['id']);
			}else if(isset($this->request['search'])){
				$this->response['data'] = searchEvents($this->request['search']);
			}else{ // get all events
				$this->response['data'] = getEvents();
			}
		}else if($this->method == 'POST'){
			if(isset($this->request['start']) && isset($this->request['end']) && isset($this->request['name']) && isset($this->request['desc'])){
				if(isset($this->request['id'])){ // an id was provided -> update
					$this->response['data'] = updateEvent($this->request['id'], $this->request['start'], $this->request['end'], $this->request['name'], $this->request['desc']);
				}else{ // an id was not provided ->insert
					$this->response['data'] = insertEvent($this->request['start'], $this->request['end'], $this->request['name'], $this->request['desc']);
				}
			}else {//unknown
				$this->response['message'] = "endpoint requires parameters. Update(id, start, end, name, desc) || Insert(start, end, name, desc)";
				$this->response['code'] = 400;
			}
		}else if($this->method == 'DELETE'){
				if(isset($this->request['id'])){
					$this->response['data'] = deleteEvent($this->request['id']);
				}else{
					$this->response['message'] = "endpoint requires  parametes. Delete(id)";
					$this->response['code'] = 400;
				}
		}else{
			$this->response['message'] = "endpoint does not recognize " . $this->method . " request";
			$this->response['code'] = 405;
		}
		return $this->response;
	}
	
	public function userEvents(){
		if($this->method == 'GET'){
			$this->response['data'] = getUserEvents();
		}else if ($this->method == 'POST'){
			if(isset($this->request['id'])){
				$this->response['data'] = insertUserEvent($this->request['id']);
			}else{
				$this->response['message'] = "endpoint requires parameters.  Insert(eventid)";
				$this->response['code'] = 400;
			}
		}else if($this->method == 'DELETE'){
			if(isset($this->request['id'])){
				$this->response['data'] = deleteUserEvent($this->request['id']);
			}else{
				$this->response['message'] = "endpoint requires parameters.  Delete(eventid)";
				$this->response['code'] = 400;
			}
		
		}else{
			$this->response['message'] = "endpoint does not recognize " . $this->method . " request";
			$this->response['code'] = 405;
		}
		return $this->response;
	}
	public function courses(){
		if($this->method == 'GET'){
			if(isset($this->request['id'])){ // get course with id
				$this->response['data'] = getCourse($this->request['id']);
			}else{ // get all courses
				$this->response['data'] = getCourses();
			}
		}else if($this->method == 'POST'){
			if(isset($this->request['event']) && isset($this->request['department']) && isset($this->request['professor']) && isset($this->request['course']) && isset($this->request['desc']) && isset($this->request['website']) && isset($this->request['days']) && isset($this->request['semester'])){
				 	$this->response['data'] = insertCourse($this->request['event'], $this->request['department'], $this->request['professor'], $this->request['course'], $this->request['desc'], $this->request['website'], $this->request['days'], $this->request['semester']);
				 }else {
					$this->response['message'] = "endpoint requires parameters.  Insert(event, department, professor, course, desc, website, days, semester)";
					$this->response['code'] = 400;
				}
			}else if($this->method == 'DELETE'){
				if(isset($this->request['id'])){
					$this->response['data'] = deleteCourse($this->request['id']);
				}else{
					$this->response['message'] = "endpoint requires  parametes. Delete(id)";
					$this->response['code'] = 400;
				}
			}else{
				$this->response['message'] = "endpoint does not recognize " . $this->method . " request";
				$this->response['code'] = 405;
		}
		
		return $this->response;
	}

}
 ?>