<?php

namespace Qwer\LottoDocumentsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Qwer\LottoDocumentsBundle\Entity\DocumentType;
use Qwer\LottoDocumentsBundle\Form\DocumentTypeType;

/**
 * DocumentType controller.
 *
 */
class DocumentTypeController extends Controller
{
    /**
     * Lists all DocumentType entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('QwerLottoDocumentsBundle:DocumentType')->findAll();

        return $this->render('QwerLottoDocumentsBundle:DocumentType:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new DocumentType entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new DocumentType();
        $form = $this->createForm(new DocumentTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('documenttype_show', array('id' => $entity->getId())));
        }

        return $this->render('QwerLottoDocumentsBundle:DocumentType:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new DocumentType entity.
     *
     */
    public function newAction()
    {
        $entity = new DocumentType();
        $form   = $this->createForm(new DocumentTypeType(), $entity);

        return $this->render('QwerLottoDocumentsBundle:DocumentType:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DocumentType entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QwerLottoDocumentsBundle:DocumentType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DocumentType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('QwerLottoDocumentsBundle:DocumentType:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing DocumentType entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QwerLottoDocumentsBundle:DocumentType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DocumentType entity.');
        }

        $editForm = $this->createForm(new DocumentTypeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('QwerLottoDocumentsBundle:DocumentType:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing DocumentType entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('QwerLottoDocumentsBundle:DocumentType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DocumentType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DocumentTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('documenttype_edit', array('id' => $id)));
        }

        return $this->render('QwerLottoDocumentsBundle:DocumentType:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DocumentType entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('QwerLottoDocumentsBundle:DocumentType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DocumentType entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('documenttype'));
    }

    /**
     * Creates a form to delete a DocumentType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
