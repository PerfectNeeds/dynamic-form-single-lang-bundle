<?php

namespace PN\DynamicFormBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use PN\Bundle\FormBundle\Entity\Section;
use PN\Bundle\FormBundle\Form\SectionType;
use PN\Bundle\FormBundle\Entity\SectionAttribute;
use PN\Bundle\FormBundle\Entity\SectionAttributeEnum;
use PN\Bundle\FormBundle\Form\SectionAttributeType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Section controller.
 *
 * @Route("form-section/attribute")
 */
class SectionAttributeController extends Controller {

    /**
     * Displays a form to edit an existing section entity.
     *
     * @Route("/{id}", name="section_attribute_index")
     * @Method("GET")
     */
    public function indexAction(Request $request, Section $section) {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $em = $this->getDoctrine()->getManager();
        $sectionAttributes = $em->getRepository('PNDynamicFormBundle:SectionAttribute')->findBySection($section->getId());

        return $this->render('@PNDynamicForm/Administration/SectionAttribute/index.html.twig', array(
                    'section' => $section,
                    'sectionAttributes' => $sectionAttributes,
        ));
    }

    /**
     * Creates a new section entity.
     *
     * @Route("/new/{id}", name="section_attribute_new")
     * @Method({"POST"})
     */
    public function newAction(Request $request, Section $section) {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $editForm = $this->createForm(SectionType::class, $section, [
            'action' => $this->generateUrl('section_edit', ["id" => $section->getId()])
        ]);

        $sectionAttr = new SectionAttribute;
        $attrForm = $this->createForm(SectionAttributeType::class, $sectionAttr, [
            'action' => $this->generateUrl('section_attribute_new', ["id" => $section->getId()])
        ]);
        $attrForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        $valid = true;
        $enumsArr = [];
        if ($sectionAttr->getType() == SectionAttribute::TYPE_ENUMS) {
            $enums = $request->request->get("enums");
            dump($enums);
            if ($enums == "") {
                $this->addFlash("error", "Please add your choices");
                $valid = false;
            } else {
                $enumsArr = explode(",", $enums);
            }
        }

        if ($attrForm->isSubmitted() && $attrForm->isValid() && $valid) {
            foreach ($enumsArr as $enum) {
                $sectionAttributeEnum = new SectionAttributeEnum;
                $sectionAttributeEnum->setSectionAttribute($sectionAttr);
                $sectionAttributeEnum->setOptionText($enum);
                $em->persist($sectionAttributeEnum);
            }

            $sectionAttr->setSection($section);
            $em->persist($sectionAttr);
            $em->flush();

            $this->addFlash("success", "Successfully added");
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
     * @Route("/{id}", name="section_attribute_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SectionAttribute $sectionAttribute) {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");
        $em = $this->getDoctrine()->getManager();
        $userName = $this->get('user')->getUserName();
        $sectionAttribute->setDeletedBy($userName);
        $sectionAttribute->setDeleted(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($sectionAttribute);
        $em->flush();

        return $this->redirectToRoute('section_attribute_index', ["id" => $sectionAttribute->getSection()->getId()]);
    }

    /**
     * Deletes a tasklist entity.
     *
     * @Route("/sort/{id}", name="section_attribute_sort")
     * @Method("POST")
     */
    public function sortAction(Request $request, Section $section) {
        if (!$section) {
            $return = ['error' => 0, "message" => 'Error'];
            return new JsonResponse($return);
        }
        $em = $this->getDoctrine()->getManager();
        $listJson = $request->request->get('json');
        $sortedList = json_decode($listJson);
        $i = 1;
        foreach ($sortedList as $key => $value) {
            if (!array_key_exists($key, $sortedList)) {
                continue;
            }
            $sortedListNod = $sortedList[$key];
            foreach ($sortedListNod as $keyNod => $valueNod) {
                if (!array_key_exists($key, $sortedList)) {
                    continue;
                }
                if (!isset($valueNod->id)) {
                    continue;
                }
                $attribute = $em->getRepository('PNDynamicFormBundle:SectionAttribute')->find($valueNod->id);
                if ($attribute->getSection()->getId() != $section->getId()) {
                    continue;
                }
                $attribute->setSort($i);
                $em->persist($attribute);
                $i++;
            }
        }
        $em->flush();

        $return = [
            'error' => 0,
            'message' => 'Successfully sorted',
        ];
        return new JsonResponse($return);
    }

}
