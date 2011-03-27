<?php
class IndexController extends Zend_Controller_Action
{
	function init()
	{
   	        $this->view->baseUrl = $this->_request->getBaseUrl();
	}  
	//Index page
	function indexAction()
	{
		$this->view->title = "Home";
                $latest = new Album();
                //Fetch latest 3 records from albums database
                $this->view->latest = $latest->fetchByLatest();
	}
	//View album page
        function viewalbumAction()
	{
		$this->view->title = "View Albums";
                //Create object for view
                $album = new View();
                //Fetch all records from albums database
		$this->view->album = $album->fetchAll();

         }
         //View jquery page
        function jqueryAction()
	{

           $this->view->title = "Jquery";
		 //Create object for Add form
		$jquery = new JQueryForm();
                 //Set Add form to view
		$this->view->jquery = $jquery;
		$jquery->setAction('/albumzend/albums/index')
		->setMethod('post');

         }

        //Add album page 
       function albumAction()
	{
		//Create object for db functions
                $album = new Album();
                $id = (int)$this->_request->getParam('id', 0);
                //Fetch perticular record from albums database based on album id
	        $result=$this->view->album = $album->fetchDetails($id);
                $this->view->title =  $result['2'];

        }
        //Search album page 
       function searchalbumAction()
	{
                $type1='';
                $sql='';
		$this->view->title = "Search Albums";
                //Create object for search form
                $searchform = new SearchForm();
                //Set Search form to view
         	$this->view->searchform = $searchform;
                $searchform->setAction('/albumzend/albums/index/searchalbum');
                //Create object for db functions
                $album = new Album();                
                //Create object for view
                $search = new View();
                
                //Check form values posted or not
		if ($this->_request->isPost()) 
		{ 
                  //Getting form post values
                  $s_type=$this->view->type = $this->_request->getPost('type');
                  $s_gender=$this->view->gender = $this->_request->getPost('gender');
                  $s_instr=$this->view->instr = $this->_request->getPost('instr');
                  //Set post valus to view form
                  $this->view->searchform->type->setValue($s_type); 
                  $this->view->searchform->gender->setValue($s_gender);       
                  $this->view->searchform->instr->setValue($s_instr);               
 
                 //Search conditions
                 if($s_type)
                  $type1= implode("','", $s_type);
                  
                 if($type1 <>'' && $s_gender <>'' && $s_instr<> ''){
                 $where="WHERE instr='".$s_instr."' AND gender='".$s_gender."' AND type in('".$type1."')";
                 }
                 elseif($type1 =='' && $s_gender <>''&& $s_instr<> '')
                 {
                   $where="WHERE instr='".$s_instr."' AND gender='".$s_gender."'";
                 } 
                 elseif($type1 <>'' && $s_gender ==''&& $s_instr<> '')
                 {
                   $where="WHERE instr='".$s_instr."' AND type in('".$type1."')";
                   
                 }
                 elseif($type1 =='' && $s_gender <>''&& $s_instr== '')
                 {
                   $where="WHERE gender='".$s_gender."'";
                 } 
                 elseif($type1 <>'' && $s_gender ==''&& $s_instr== '')
                 {
                   $where="WHERE  type in('".$type1."')";
                   
                 }
              elseif($type1 <>'' && $s_gender <>''&& $s_instr== '')
                 {
                   $where="WHERE  gender='".$s_gender."' AND type in('".$type1."')";
                   
                 }
                elseif($type1 =='' && $s_gender ==''&& $s_instr<> '')
                 {

                   $where="WHERE instr='".$s_instr."'";
                   
                 }
                else{
                   //In default list all records
                   $where="";

                  }
                 echo $where;
                 $this->view->album = $album->fetchBySearch($where);

                }
           else{
                //In default list all records
		 $this->view->album = $album->fetchBySearch($where='');
           }

       }
       function deletealbumAction()
	{
	       $this->view->title = "Delete Album";
               //Create object for view
               $album = new View();
               //Create object for delete form
               $deleteform = new DeleteForm();
               $id = (int)$this->_request->getParam('id', 0);
                //Set Delete form to view
	       $this->view->deleteform = $deleteform;
	       $deleteform->setAction('/albumzend/albums/index/deletealbum/id/'.$id)
		->setMethod('post');
             
               //Fetch perticular record from albums database based on album id
               $this->view->album = $album->fetchRow('album_id='.$id);
                $where = 'album_id = ' . $id;
                if ($this->_request->isPost()) 
		{
                $action_yes = $this->_request->getPost('Yes');
                $action_no = $this->_request->getPost('No');

                   if($action_yes=="Yes"){  
                    //Delete record based on album id
                    $rows_affected = $album->delete($where);
                    //Redirect to view album
                     $this->_redirect('/index/viewalbum');		
                    }
                   if($action_no=="No"){     
                    $this->_redirect('/index/viewalbum');	
                     }

		}
         	
	 }

   function editalbumAction()
    	{
       	
	        $this->view->title = "Edit Album";
                 //Create object for view
		$edit = new View();
		$albumtype='';
                 //Create object for Add form
                $editform = new AddalbumForm();
                //Set Add form to view
		$this->view->editform = $editform;
                $editform->setAction('/albumzend/albums/index/editalbum')
     		->setMethod('post');
                 $editform->addElement('submit', 'Add', array(
			    'label' => 'Update',
			
			));

                   //Getting album id
           	 $id = (int)$this->_request->getParam('id', 0);
               if ($this->_request->isPost()) 
		   {
                        //Getting form post values
                	$albumid = $this->_request->getPost('albumid');
                        $name = $this->_request->getPost('albumname');
                        $type = $this->_request->getPost('type');
			$gender = $this->_request->getPost('gender');
                        $instr = $this->_request->getPost('instr');
                        $year = $this->_request->getPost('year');
                        $picture = $this->_request->getPost('pic');
                    $albumtype=implode($type,',');

 		 if ($albumid !== '') 
			{
                   $data = array('album_id' => $albumid,'name' => $name,'type'=>$albumtype,'gender'=>$gender,
                                 'instr' => $instr,'year'=>$year,'picture'=>$picture);
			  $where = 'album_id = ' . $albumid;
                           //Update record based on album id
             		  $edit->update($data, $where);
             	         $this->_redirect('/index/viewalbum');
             				return;
   
    			}
                 
		      } 
		   else 
		     {
		      //Set the value to edit form
       			$result = $edit->fetchRow('album_id='.$id); 
                        $type=explode(",",$result->type);
                        $this->view->editform->albumid->setValue($result->album_id);
                        $this->view->editform->albumname->setValue($result->name);
                        $this->view->editform->gender->setValue($result->gender);
			$this->view->editform->instr->setValue($result->instr);
                        $this->view->editform->type->setValue($type);
			$this->view->editform->year->setValue($result->year);

		 }
        }

	function addalbumAction()
	{
		$this->view->title = "Add Album";
		 //Create object for Add form
		$addform = new AddalbumForm();
                 //Set Add form to view
		$this->view->addform = $addform;
		$addform->setAction('/albumzend/albums/index/addalbum')
		->setMethod('post');
             
		if ($this->_request->isPost()) 
     	         {
	    	 $formData = $this->_request->getPost();
	    	  if ($addform->isValid($formData)) 
	    	   {
                      $albumtype='';
	    	        $addform->populate($formData);
                       //Getting form post values
                        $album_id = $addform->getvalue('albumid');
	    		$album_name = $addform->getvalue('albumname');
                        $type = $addform->getvalue('type');
			$gender = $addform->getvalue('gender');
			$instr = $addform->getvalue('instr');
			$year = $addform->getvalue('year');
                        $picture=$addform->getvalue('pic');

       	          $albumtype=implode($type,',');
                  $album = new Album();
                  $check=$album->fetchDetails($album_id);
                  //Check record exist or not
                  if(empty($check)){
                  //Add record
                   $result = $album->insert_row(array(
                                           'album_id'=>$album_id,
					   'name' => $album_name,
					   'type' => $albumtype,
                                           'gender' => $gender,
					   'instr' => $instr,
					   'year' => $year,
                                           'picture'=>$picture,
					  ));
                   //After added record
                   if($result){
                   echo "<div align='center' style='font-size:14px;'>Successfully album ".$album_name." added.</div>";
                    $this->view->addform->albumid->setValue('');
                   $this->view->addform->albumname->setValue('');
                   $this->view->addform->type->setValue('');
                    $this->view->addform->gender->setValue('');
                   $this->view->addform->instr->setValue('');
                    $this->view->addform->year->setValue('');
      
		               
                    }
                  }
                 else{
                   echo "</div>Already existing this album id ".$album_id."</div>";
                    }               }
	}
    }
	
}
