<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DuplicatedDocumentException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Category;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\CategoryManager;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class CategoryController extends AbstractWnttRestController
{
    /**
     * Returns collection of Category objects.
     *
     * @QueryParam(name="noPaging", nullable=true, default=false, description="set to true if you want to retrieve all records without paging")
     *
     * @param ParamFetcher $paramFetcher
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
    public function getCategoriesAction(ParamFetcher $paramFetcher, Request $request)
    {
        $categories = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
            ->findAll();

        $paginator  = $this->get('knp_paginator');
        $paginatedCategories = $paginator->paginate(
            $categories,
            $request->query->getInt('page', 1),
            $paramFetcher->get('noPaging') === 'true' ? PHP_INT_MAX : $this->container->getParameter('api_list_items_per_page')
        );

        $view = $this->view($paginatedCategories, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Category object by given ID.
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
        /** @var $category Category */
        $category = $this->verifyDocumentExists($id, 'Category');

        $view = $this->view($category, 200);
        return $this->handleView($view);
    }

    /**
     * Creates new Category object. ROLE_USER is minimum required role to perform this action.
     *
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

        try {
            /** @var $categoryManager CategoryManager */
            $categoryManager = $this->get('wnttapi.manager.category');
            $category = $categoryManager->createDocument($categoryData, ['name' => $categoryData['name']]);
        } catch(DuplicatedDocumentException $e) {
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($category, 201);
        return $this->handleView($view);
    }

    /**
     * Updates existing Category object with given ID. ROLE_ADMIN is minimum required role to perform this action.
     *
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
        $category = $this->verifyDocumentExists($id, 'Category');

        $categoryData = $this->retrieveCategoryData($request);
        $this->validateCategoryData($categoryData);

        try {
            /** @var $categoryManager CategoryManager */
            $categoryManager = $this->get('wnttapi.manager.category');
            $category = $categoryManager->updateDocument($category, $categoryData);
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($category, 200);
        return $this->handleView($view);
    }

    /**
     * Deletes existing Category object with given ID. ROLE_ADMIN is minimum required role to perform this action.
     *
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
        $category = $this->verifyDocumentExists($id, 'Category');

        try {
            /** @var $categoryManager CategoryManager */
            $categoryManager = $this->get('wnttapi.manager.category');
            $categoryManager->deleteDocument($category);
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

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
