<?php
namespace Potool\Controller;

use Potool\Model\ModuleManager;


/**
 * Work with languages
 * User: eugene
 * Date: 3/21/13
 * Time: 5:45 PM
 * To change this template use File | Settings | File Templates.
 */
class LanguageController extends AbstractController
{


    function indexAction()
    {
        $module = $this->getModuleByParams();
        $languages = $module->getLanguages();

        return array('module'=>$module, 'languages'=>$languages, 'flashMessages'=>$this->flashMessenger()->getMessages());
    }

    function addAction()
    {
        $module = $this->getModuleByParams();
        $form = new \Potool\Form\LanguageAdd();
        $request = $this->getRequest();
        $error = '';
        if($request->isPost()){
            try{
                $form->setData($request->getPost());
                if (!$form->isValid()){
                    throw new \Exception('Form is invalid');
                }
                $language = $module->createLanguage($form->get('name')->getValue());
                return $this->redirectToIndex($module, sprintf($this->translate('Language "%s" has been successfully added'), $language));
            }catch(\Exception $e){
                $error = $e->getMessage();
            }
        }else{
            $form->get('module')->setValue($module);
        }
        return array('form'=>$form, 'error'=>$error, 'module'=>$module);
    }

    /**
     * Redirect to language list of current module
     * @param \Potool\Model\Module $module
     * @param null|string $message
     * @return mixed
     */
    protected function redirectToIndex($module, $message = null)
    {
        if ($message){
            $this->flashMessenger()->addMessage($message);
        }
        return $this->redirect()->toRoute('potool', array('controller'=>'language', 'action'=>'index', 'id'=>$module->getName()));
    }

    function deleteAction()
    {
        $module = $this->getModuleByParams();
        $language = $module->getLanguageByName($this->params()->fromRoute('language'));
        $request = $this->getRequest();
        if($request->isPost()){
            if ($request->getPost('answer')){
                $language->delete();
                $this->flashMessenger()->addMessage(sprintf($this->translate('Language "%s" was delete'), $language));
            }
            return $this->redirectToIndex($module);
        }
        return array('module'=>$module, 'language'=>$language);
    }

    function upgradeAction()
    {
        $module = $this->getModuleByParams();
        if (!$module->isLanguageDirWritable()){
            throw new \Exception(sprintf($this->translate('Language folder of module "%s" is not writable'), $module));
        }
        $language = $module->getLanguageByName($this->params()->fromRoute('language'));
        $pot = $module->createPot();
        $language->merge($pot);
        return $this->redirectToIndex($module, sprintf($this->translate('Language %s was successfully upgraded'), $language));
    }

    function compileAction()
    {
        $module = $this->getModuleByParams();
        if (!$module->isLanguageDirWritable()){
            throw new \Exception(sprintf($this->translate('Language folder of module "%s" is not writable'), $module));
        }
        $language = $module->getLanguageByName($this->params()->fromRoute('language'));
        $language->compile();
        return $this->redirectToIndex($module, sprintf($this->translate('Language %s was successfully upgraded'), $language));
    }

    function translateAction()
    {
        $module = $this->getModuleByParams();
        if (!$module->isLanguageDirWritable()){
            throw new \Exception(sprintf($this->translate('Language folder of module "%s" is not writable'), $module));
        }
        $language = $module->getLanguageByName($this->params()->fromRoute('language'));
        $entries = $language->getEntries();

        $error = '';
        $request = $this->getRequest();
        if ($request->isPost()){
            try{
                $posted = $request->getPost('entry');
                if (!count($posted)){
                    throw new \Exception($this->translate("Entries not found"));
                }
                $updated = array();
                $deleted = array();
                foreach($posted as $postedItem){
                    $entry = new \Potool\Model\PO\Entry($postedItem);

                    if (!$entry->verifyHash($postedItem['hash'])){
                        $updated[$entry->msgid]=$entry;
                    }

                    if (isset($postedItem['delete']) && (int)$postedItem['delete']){
                        $deleted []= $entry->msgid;
                    }
                }



                if (!count($updated) && !count($deleted)){
                    throw new \Exception($this->translate("Nothing was updated!"));
                }

                if (count($updated)){
//                    var_dump($updated); exit;
                    foreach($entries as $entry){
                        if (!$entry->msgid) {
                            continue;
                        }
                        if (!isset($updated[$entry->msgid])){
                            continue;
                        }
                        $updatedEntry = $updated[$entry->msgid];
                        $entry->msgstr = $updatedEntry->msgstr;
                        $entry->comments = $updatedEntry->comments;
                        $entry->setFuzzy($updatedEntry->getFuzzy());
                    }
                    $message = sprintf($this->translate('%s translated phrases for language "%s" have been saved'), count($updated), $language);
                    $this->flashMessenger()->addMessage($message);
                }

                if (count($deleted))
                {
                    //@TODO
                }

                $language->updateEntries();
                return $this->redirect()->toRoute('potool', array('controller'=>'language', 'action'=>'index', 'id'=>$module->getName()));
            }catch(\Exception $e){
                $error = $e->getMessage();
            }
        }

        return array('module' => $module, 'language' => $language, 'entries' => $entries, 'error' => $error);
    }

    function addMessageAction()
    {
        $module = $this->getModuleByParams();
        if (!$module->isLanguageDirWritable()){
            throw new \Exception(sprintf($this->translate('Language folder of module "%s" is not writable'), $module));
        }

        $request = $this->getRequest();
        $error = '';
        if ($request->isPost()){
            try{
                $post = $request->getPost();
                if (!$post['key']){
                    throw new \Exception($this->translate('Message key can not be empty'));
                }
                $module->addMessageKey($post['key']);

                if (isset($post['add-and-exit'])){
                    $module->upgrade();
                    $message = sprintf($this->translate('Key "%s" was successfully added and module has been upgraded'), $key);
                    return $this->redirectToIndex($module, $message);
                }else{
                    $this->flashMessenger()->addMessage(sprintf($this->translate('Key "%s" was successfully added'), $key));
                    return $this->redirect()->toRoute('potool', array('controller'=>'language', 'action'=>'add-message', 'id'=>$module->getName()));
                }

            }catch(\Exception $e){
                $error = $e->getMessage();
            }
        }

        return array('module'=>$module, 'error'=>$error);
    }
}
