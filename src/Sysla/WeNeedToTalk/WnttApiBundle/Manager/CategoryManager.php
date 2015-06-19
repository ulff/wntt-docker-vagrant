<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Category;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;

class CategoryManager extends AbstractDocumentManager
{
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'Category';
        parent::__construct($documentManager);
    }

    protected function createDocumentFromRequest(array $categoryData)
    {
        $category = new Category();
        $this->updateDocumentFromRequest($category, $categoryData);

        return $category;
    }

    protected function updateDocumentFromRequest(Document $category, array $categoryData)
    {
        /** @var $category Category */
        $category->setName($categoryData['name']);
    }

    protected function validateDocumentData(array $categoryData, Document $category = null)
    {
    }
}