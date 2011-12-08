<?php

class dmBehaviorsFrameworkActions extends dmBaseActions {

    public function executeForm(dmWebRequest $request) {
        $behaviorSettings = $this->getService('behaviors_manager')->getBehaviorSettings($request->getParameter('dmBehaviorKey'));
        $form = null;
        try {
            $form = new $behaviorSettings['dmBehaviorForm'];
        } catch (Exception $e) {
            return null; // TODO THROW USER ERROR HERE
        } 
        $action = $request->getParameter('dmBehaviorFormAction');        
        if ($action == 'save') {
            $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
            if ($form->isValid()) {
                $this->response->setContentType('text/html');
                $tmp = $request->getParameter($form->getName());
                unset($tmp['_csrf_token']);
                $tmp['dmBehaviorKey'] = $behaviorSettings['dmBehaviorKey'];
                $tmp['dmBehaviorName'] = $behaviorSettings['dmBehaviorName'];
                $tmp['dmBehaviorIcon'] = $behaviorSettings['dmBehaviorIcon'];
                $this->response->setContent(json_encode($tmp));
                return sfView::NONE;
            } else {
                return $this->renderAsync(array(
                    'html'=>$form->open(array('class'=>'dm_form list little')) . $form->render() . $this->renderButtons() . $form->close(),
                    'css'=>$form->getStylesheets(),
                    'js'=>$form->getJavascripts()
                ), true);
            }
        }
        if ($action == 'edit') {
            $behaviorData = $request->getParameter('dmBehaviorData');            
            $behaviorData['_csrf_token'] = $form->getCSRFToken();
            $form->bind($behaviorData);
        }
        return $this->renderAsync(array(
                'html' => $form->open(array('class' => 'dm_form list little')) . $form->render() . $this->renderButtons() . $form->close(),
                'css' => $form->getStylesheets(),
                'js' => $form->getJavascripts()
            ), true);
    }

    private function renderButtons() {
        return $this->getHelper()->renderPartial('dmBehaviorsFramework', 'buttons');
    }

}
