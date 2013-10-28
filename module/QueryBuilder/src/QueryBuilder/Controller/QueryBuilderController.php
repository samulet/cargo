<?php

namespace QueryBuilder\Controller;

use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Mvc\Controller\AbstractActionController;

class QueryBuilderController extends AbstractActionController
{
    protected $queryBuilderModel;

    public function getQueryBuilderModel()
    {
        if (!$this->queryBuilderModel) {
            $sm = $this->getServiceLocator();
            $this->queryBuilderModel = $sm->get('QueryBuilder\Model\QueryBuilderModel');
        }
        return $this->queryBuilderModel;
    }

}
