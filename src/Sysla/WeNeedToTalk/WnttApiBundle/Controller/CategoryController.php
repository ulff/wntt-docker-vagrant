<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Category;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\CategoryManager;

class CategoryController extends FOSRestController
{
    /**
     * Returns collection of Category objects
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Category objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getCategoriesAction()
    {
        $categories = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
            ->findAll();

        $view = $this->view($categories, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Category object by given ID
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns Category object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getCategoryAction($id)
    {
        $category = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($category, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Creates Category object",
     *   parameters={
     *      {"name"="name", "dataType"="string", "description"="category name", "required"=true}
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function postCategoryAction(Request $request)
    {
        $categoryData = $this->retrieveCategoryData($request);
        $this->validateCategoryData($categoryData);

        /** @var $categoryManager CategoryManager */
        $categoryManager = $this->get('wnttapi.manager.category');
        $category = $categoryManager->createDocument($categoryData, ['name' => $categoryData['name']]);

        $view = $this->view($category, 201);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Updates Category object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="category id"}
     *   },
     *   parameters={
     *      {"name"="name", "dataType"="string", "description"="category name", "required"=true}
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function putCategoryAction(Request $request, $id)
    {
        /** @var $category Category */
        $category = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
            ->find($id);

        if (empty($category)) {
            throw $this->createNotFoundException('No category found for id '.$id);
        }

        $categoryData = $this->retrieveCategoryData($request);
        $this->validateCategoryData($categoryData);

        /** @var $categoryManager CategoryManager */
        $categoryManager = $this->get('wnttapi.manager.category');
        $category = $categoryManager->updateDocument($category, $categoryData);

        $view = $this->view($category, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Deletes Category object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="category id"}
     *   },
     *   statusCodes={
     *      200="Returned when successfully deleted",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function deleteCategoryAction($id)
    {
        /** @var $category Category */
        $category = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
            ->find($id);

        if (empty($category)) {
            throw $this->createNotFoundException('No category found for id '.$id);
        }

        /** @var $categoryManager CategoryManager */
        $categoryManager = $this->get('wnttapi.manager.category');
        $categoryManager->deleteDocument($category);

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    protected function retrieveCategoryData(Request $request)
    {
        return [
            'name' => $request->get('name')
        ];
    }

    protected function validateCategoryData($categoryData)
    {
        if (empty($categoryData['name'])) {
            throw new HttpException(400, 'Missing required parameters: name');
        }
    }
}