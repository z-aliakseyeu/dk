<?php

namespace Dk\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dk\AppBundle\Form\Type\CategoryType;

/**
 * @author Zmicier Aliakseyeu <z.aliakseyeu@gmail.com>
 */
class CategoryController extends Controller
{
    /**
     * @Route("/admin/category/list", name="dk_admin_category_list")
     * @Template("DkAppBundle:Backend/Category:list.html.twig")
     */
    public function listAction()
    {
        $items = $this->_getRepository()->getAll();

        return array('items' => $items);
    }

    /**
     * @Route("/admin/category/add", name="dk_admin_category_add")
     * @Template("DkAppBundle:Backend/Category:add.html.twig")
     */
    public function addAction()
    {
        $form = $this->_getForm();

        $result = $this->_processForm($form);
        if (false !== $result) {
            return $result;
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/admin/category/{id}/edit", name="dk_admin_category_edit", requirements={"id" = "\d+"})
     * @Template("DkAppBundle:Backend/Category:edit.html.twig")
     */
    public function editAction($id)
    {
        $item = $this->_getRepository()->get($id);

        if (!$item) {
            throw $this->createNotFoundException('Категория не была найдена!');
        }

        $form = $this->_getForm($item);
        
        $result = $this->_processForm($form);

        if (false !== $result) {
            return $result;
        }

        return array(
            'form' => $form->createView(),
            'item' => $item,
        );
    }

    /**
     * @Route("/admin/category/{id}/delete", name="dk_admin_category_delete", requirements={"id" = "\d+"})
     * @Template("DkAppBundle:Backend/Category:list.html.twig")
     */
    public function deleteAction($id)
    {
        $item = $this->_getRepository()->get($id);

        if (!$item) {
            throw $this->createNotFoundException('Товар не был найден!');
        }

        $em = $this->getDoctrine()->getManager();

        try {
            $em->remove($item);
            $em->flush();
            $this->getRequest()->getSession()->getFlashBag()->add(
                'success', 'Операция прошла успешно'
            );
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->getFlashBag()->add(
                'error', 'При выполнении операции произошла ошибка'
            );
        }

        return $this->redirect($this->generateUrl('dk_admin_category_list'));
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function _getRepository()
    {
        return $this
            ->getDoctrine()
            ->getRepository('Dk\AppBundle\Entity\Warehouse\Category')
        ;
    }

    /**
     * 
     */
    private function _getForm($data = null)
    {
        return $this->createForm(new CategoryType(), $data);
    }

    /**
     * 
     */
    private function _processForm($form)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->getRequest()->isMethod('post')) {
            $form->submit($this->getRequest());
            if ($form->isValid()) {
                $item = $form->getData();
                $em->persist($item);
                $em->flush();

                $this->getRequest()->getSession()->getFlashBag()->add(
                    'success', 'Операция прошла успешно'
                );

                return $this->redirect($this->generateUrl('dk_admin_category_list'));
            } else {
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'error', 'При выполнении операции произошла ошибка'
                );
            }
        }

        return false;
    }
}