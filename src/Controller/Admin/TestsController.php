<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Entity\Medicine;
use Cake\Filesystem\File;

/**
 * Tests Controller
 *
 * @property \App\Model\Table\TestsTable $Tests */
class TestsController extends AppController
{
    public $components = ['FileHandler'];

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $session = $this->request->session();

        if(isset($this->request->query['search']) and trim($this->request->query['search'])!='' ) {
            $session->write('tests_search_query', $this->request->query['search']);
        }
        if($session->check('tests_search_query')) {
            $search = $session->read('tests_search_query');
        }else{
            $search = '';
        }

        $where = $this->__search();

        if($where){
            $query = $this->Tests->find('All')->where($where);
        }else{
            $query = $this->Tests;
        }

        $this->paginate = [
            'limit' => 30,
            'order' => [
                'Tests.id' => 'desc'
            ]
        ];
        $tests = $this->paginate($query);

        if(count($tests)==0){
            $this->Flash->adminWarning(__('No examinations found!')  ,['key' => 'admin_warning'], ['key' => 'admin_warning'] );
        }

        $this->set(compact('tests', 'search'));
        $this->set('_serialize', ['tests']);
    }

    /**
     * View method
     *
     * @param string|null $id Test id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $test = $this->Tests->get($id, [
            'contain' => []
        ]);

        $this->set('test', $test);
        $this->set('_serialize', ['test']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $test = $this->Tests->newEntity();
        if ($this->request->is('post')) {
            $test = $this->Tests->patchEntity($test, $this->request->data);
            if ($this->Tests->save($test)) {
                $success_message = __('The examination has been saved.');
                $this->Flash->adminSuccess($success_message, ['key' => 'admin_success']);
            } else {
                $error_message = __('The examination could not be save. Please, try again.');
                $this->Flash->adminError($error_message, ['key' => 'admin_error']);
            }
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('test'));
        $this->set('_serialize', ['test']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Test id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $test = $this->Tests->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $test_exit = $this->Tests->find('all')
                ->where([
                    'Tests.name' => trim($this->request->data['name']),
                    'Tests.id !=' => $id
                ])
                ->first();

            if(empty($test_exit)){
                $test = $this->Tests->patchEntity($test, $this->request->data);
                if ($this->Tests->save($test)) {
                    $success_message = __('The examination has been edited.');
                    $this->Flash->adminSuccess($success_message, ['key' => 'admin_success']);
                } else {
                    $error_message = __('The examination could not be edit. Please, try again.');
                    $this->Flash->adminError($error_message, ['key' => 'admin_error']);
                }
                return $this->redirect(['action' => 'index']);
            }else{
                $this->Flash->adminWarning(__('The examination already exit'), ['key' => 'admin_warning']);
                return $this->redirect(['action' => 'edit/'.$id]);
            }
        }
        $this->set(compact('test'));
        $this->set('_serialize', ['test']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Test id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $test = $this->Tests->get($id);
        if ($this->Tests->delete($test)) {
            $success_message = __('The examination has been deleted.');
            $this->Flash->adminSuccess($success_message, ['key' => 'admin_success']);
        } else {
            $error_message = __('The examination could not be delete. Please, try again.');
            $this->Flash->adminError($error_message, ['key' => 'admin_error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    function __search(){
        $session = $this->request->session();
        if($session->check('tests_search_query')){
            $search = $session->read('tests_search_query');
            $where = [
                'OR' => [
                    ['Tests.name LIKE' => '%' . $search . '%']
                ]
            ];
        }else{
            $where = [];
        }
        return $where;
    }

    function reset(){
        $session = $this->request->session();
        $session->delete('tests_search_query');
        $this->redirect(['action' => 'index']);
    }

    function isTestAvailable(){
        $this->autoRender = false;
        $test = $this->Tests->findByName($this->request->data['name'])->toArray();
        if(empty($test)){
            echo 'true';die;
        }else{
            echo 'false';die;
        }

    }

    function importCsv(){
        if( isset($this->request->data['csv_file']) ){
            // start resume  up
            $import_test = $this->request->data['csv_file'];
            $fileInfo = pathinfo($import_test['name']);

            if($fileInfo['extension'] == 'csv'){
                if ($import_test) {
                    $result = $this->FileHandler->uploadfile($import_test);
                    if ($result) {
                        $import_test= $this->FileHandler->_uploadimgname;

                        // Set path to CSV file
                        $csv = $this->readCSV('uploads/tests/'.$import_test);

                        foreach($csv as $values){
                            if(!empty($values)){
                                foreach($values as $value){
                                    $isExit = $this->Tests->findByName(preg_replace('/\s+/', ' ', $value))->toArray();
                                    if(empty($isExit)){
                                        $test = $this->Tests->newEntity();
                                        $test = $this->Tests->patchEntity($test, $this->makeSaveRecordPattern(preg_replace('/\s+/', ' ', $value)));
                                        $this->Tests->save($test);
                                    }
                                }
                            }
                        }
                        /*$file = new File(WWW_ROOT.DS. 'uploads'.DS. 'tests' .DS. $import_test);
                        $file->delete();*/

                        $success_message = __('Examinations import successfully.');
                        $this->Flash->adminSuccess($success_message, ['key' => 'admin_success']);
                    }else {
                        $error_message = __('There was a problem. Please, try again.');
                        $this->Flash->adminError($error_message, ['key' => 'admin_error']);
                    }
                }
            }else{
                $error_message = __('Please upload a csv File');
                $this->Flash->adminError($error_message, ['key' => 'admin_error']);
            }
            return $this->redirect(['action' => 'import_csv']);
        }
    }

    function readCSV($csvFile){
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle) ) {
            $line_of_text[] = fgetcsv($file_handle, 1024);
        }
        fclose($file_handle);
        return $line_of_text;
    }

    function makeSaveRecordPattern($value){
        $medicine = [];
        $medicine['name'] = $value;
        return $medicine;
    }

    function testList($search = null){
        $this->autoRender = false;

        $tests_new = [];
        if($search){
            $tests = $this->Tests->find('list')->where(['Tests.name LIKE' => $search . '%']);
            if($tests){
                foreach($tests as $k => $v){
                    $tests_new[] = ['value'=> $k, 'text'=> $v];
                }
            }
        }

        echo json_encode($tests_new); die;
    }

}
