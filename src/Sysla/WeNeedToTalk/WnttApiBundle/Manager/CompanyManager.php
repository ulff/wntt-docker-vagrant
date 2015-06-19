<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;

class CompanyManager extends AbstractDocumentManager
{
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'Company';
        parent::__construct($documentManager);
    }

    protected function createDocumentFromRequest(array $companyData)
    {
        $company = new Company();
        $this->updateDocumentFromRequest($company, $companyData);

        return $company;
    }

    protected function updateDocumentFromRequest(Document $company, array $companyData)
    {
        /** @var $company Company */
        $company->setName($companyData['name']);
        $company->setWebsiteUrl($companyData['websiteUrl']);
        $company->setLogoUrl($companyData['logoUrl']);
    }

    protected function validateDocumentData(array $companyData, Document $company = null)
    {
    }
}