<?php

namespace PN\DynamicFormBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use PN\Bundle\FormBundle\Entity\Section;
use PN\Bundle\FormBundle\Form\SectionType;
use PN\Bundle\FormBundle\Entity\SectionAttribute;
use PN\Bundle\FormBundle\Form\SectionAttributeType;

/**
 * Section controller.
 *
 * @Route("form-section")
 */
class SectionController extends Controller {

    /**
     * Lists all section entities.
     *
     * @Route("/", name="section_index")
     * @Method("GET")
     */
    public function indexAction() {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $em = $this->getDoctrine()->getManager();

        $sections = $em->getRepository('PNDynamicFormBundle:Section')->findAll();

        return $this->render('@PNDynamicForm/Administration/Section/index.html.twig', array(
                    'sections' => $sections,
        ));
    }

    /**
     * Creates a new section entity.
     *
     * @Route("/new", name="section_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($section);
            $em->flush();

            return $this->redirectToRoute('section_edit', array('id' => $section->getId()));
        }

        return $this->render('@PNDynamicForm/Administration/Section/new.html.twig', array(
                    'section' => $section,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing section entity.
     *
     * @Route("/{id}/edit", name="section_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Section $section) {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $editForm = $this->createForm(SectionType::class, $section, [
            'action' => $this->generateUrl('section_edit', ["id" => $section->getId()])
        ]);
        $editForm->handleRequest($request);

        $sectionAttr = new SectionAttribute;
        $attrForm = $this->createForm(SectionAttributeType::class, $sectionAttr, [
            'action' => $this->generateUrl('section_attribute_new', ["id" => $section->getId()])
        ]);
        $attrForm->handleRequest($request);


        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('section_edit', array('id' => $section->getId()));
        }
        $sectionAttributes = $em->getRepository('PNDynamicFormBundle:SectionAttribute')->findBySection($section->getId());

        return $this->render('@PNDynamicForm/Administration/Section/edit.html.twig', array(
                    'section' => $section,
                    'sectionAttributes' => $sectionAttributes,
                    'edit_form' => $editForm->createView(),
                    'attr_form' => $attrForm->createView(),
        ));
    }

    /**
     * Deletes a section entity.
     *
     * @Route("/{id}", name="section_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Section $section) {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('user')->getUserName();
        $section->setDeletedBy($userName);
        $section->setDeleted(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($section);
        $em->flush();

        return $this->redirectToRoute('section_index');
    }

    /**
     * Lists all dynamicPage entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="section_datatable")
     * @Method("GET")
     */
    public function dataTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $srch = $request->query->get("search");
        $start = $request->query->get("start");
        $length = $request->query->get("length");
        $ordr = $request->query->get("order");


        $search = new \stdClass;
        $search->string = $srch['value'];
        $search->deleted = 0;
        $search->ordr = $ordr[0];

        $count = $em->getRepository('PNDynamicFormBundle:Section')->filter($search, TRUE);
        $entities = $em->getRepository('PNDynamicFormBundle:Section')->filter($search, FALSE, $start, $length);

        return $this->render("@PNDynamicForm/Administration/Section/datatable.json.twig", array(
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "entities" => $entities,
                        )
        );
    }

}
