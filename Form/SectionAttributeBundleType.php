<?php

namespace PN\DynamicFormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use PN\Bundle\FormBundle\Entity\SectionAttribute;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use PN\Utils\Date;

class SectionAttributeBundleType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $sectionAttributes = $options["data"]["sectionAttributes"];
        $workExperienceEnabled = $options["data"]["workExperience"]["enabled"];
        $workExperiences = $options["data"]["workExperience"]["data"];


        foreach ($sectionAttributes as $attribute) {

            $lable = $attribute->getFieldName();
            $attr = ["placeholder" => $attribute->getHint()];
            $inputValue = $attribute->getValue();
            $inputOptions = ["label" => $lable, "data" => $inputValue, "required" => false, "attr" => $attr, "data_class" => null];


            if ($attribute->getMandatory()) {
                $inputOptions["required"] = true;
                $inputOptions["constraints"][] = new NotNull();
            }

            switch ($attribute->getType()) {
                case SectionAttribute::TYPE_TEXT:
                    $inputType = TextType::class;
                    $inputOptions["attr"]["maxlength"] = 100;
                    $inputOptions["constraints"][] = new Length(["min" => 0, "max" => 100]);
                    break;
                case SectionAttribute::TYPE_LONGTEXT:
                    $inputType = TextareaType::class;
                    break;
                case SectionAttribute::TYPE_LINK:
                    $inputType = UrlType::class;
                    $inputOptions["constraints"][] = new Url();
                    break;
                case SectionAttribute::TYPE_IMAGE:
                    $inputType = FileType::class;
                    $attr["accept"] = "image/*";
                    $inputOptions["attr"] = $attr;

                    $allowMimeType = ["image/gif", "image/jpeg", "image/jpg", "image/png"];
                    $fileContraints = new File();
                    $fileContraints->maxSize = "2m";
                    $fileContraints->mimeTypes = $allowMimeType;
                    $fileContraints->mimeTypesMessage = "IInvalid file type! Allowed type is .png, .jpg, .jpeg";
                    $inputOptions["constraints"] = [
                        $fileContraints,
                    ];
                    break;
                case SectionAttribute::TYPE_DOCUMENT:
                    $inputType = FileType::class;
                    $allowMimeType = ["application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/mspowerpoint", "application/powerpoint", "application/vnd.ms-powerpoint", "application/x-mspowerpoint", "application/pdf", "application/excel", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
                    $attr["accept"] = ".xlsx,.xls,.doc, .docx,.pdf";
                    $inputOptions["attr"] = $attr;

                    $fileContraints = new File();
                    $fileContraints->maxSize = "2m";
                    $fileContraints->mimeTypes = $allowMimeType;
                    $fileContraints->mimeTypesMessage = "Invalid file type! Allowed type is  .doc, .docx,.pdf";
                    $inputOptions["constraints"] = [
                        $fileContraints,
                    ];
                    break;
                case SectionAttribute::TYPE_EMAIL:
                    $inputType = EmailType::class;
                    $inputOptions["constraints"][] = new Email();
                    break;
                case SectionAttribute::TYPE_DATE:
                    $inputType = TextType::class;
                    $inputOptions["attr"]["class"] = "pickadate";
                    break;
                case SectionAttribute::TYPE_PAST_DATE:
                    $inputType = TextType::class;
                    $inputOptions["attr"]["class"] = "pickadate-past";
                    break;
                case SectionAttribute::TYPE_FUTURE_DATE:
                    $inputType = TextType::class;
                    $inputOptions["attr"]["class"] = "pickadate-future";
                    break;
                case SectionAttribute::TYPE_NUMBER:
                    $inputType = IntegerType::class;
                    $inputOptions["constraints"][] = new GreaterThanOrEqual(0);
                    $inputOptions["attr"]["min"] = 0;
                    break;
                case SectionAttribute::TYPE_BOOLEAN:
                    $inputType = ChoiceType::class;
                    $inputOptions["placeholder"] = "Choose an option";
                    $inputOptions["choices"] = [
                        "Yes" => "Yes",
                        "No" => "No"
                    ];
                    break;
                case SectionAttribute::TYPE_ENUMS:
                    $inputType = ChoiceType::class;
                    if ($attribute->getSectionAttributeEnums()->count() > 0) {
                        $inputOptions["placeholder"] = "Choose an option";
                        foreach ($attribute->getSectionAttributeEnums() as $choice) {
                            $inputOptions["choices"][$choice->getOptionText()] = $choice->getOptionText();
                        }
                    } else {
                        $inputType = null;
                    }
                    break;
                default :
                    $inputType = null;
                    break;
            }
            if ($inputType !== null) {
                $builder->add($attribute->getId(), $inputType, $inputOptions);

                if (in_array($attribute->getType(), [SectionAttribute::TYPE_DATE, SectionAttribute::TYPE_PAST_DATE, SectionAttribute::TYPE_FUTURE_DATE])) {
                    $builder->get($attribute->getId())
                            ->addModelTransformer(new CallbackTransformer(
                                    function ($date) {
                                if ($date == NULL) {
                                    return NULL;
                                }
                                $dateTime = \DateTime::createFromFormat("Y-m-d", $date);

                                // transform the DateTime to a string
                                return $dateTime->format("d/m/Y");
                            }, function ($date) {
                                if ($date == NULL) {
                                    return NULL;
                                }
                                $dateTime = Date::convertDateFormatToDateTime($date, Date::DATE_FORMAT3);
                                // transform the string back to DateTime
                                return $dateTime->format(Date::DATE_FORMAT2);
                            })
                    );
                }
            }
        }
        if ($workExperienceEnabled == true) {
            $builder->add("workExperiences", CollectionType::class, array(
                "entry_type" => WorkExperienceType::class,
                "allow_add" => true,
                "prototype" => true,
                "label" => false,
                "data" => $workExperiences,
                // Post update
                "by_reference" => false,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            "data_class" => null,
            "sectionAttributes" => [],
            "workExperience" => [
                "enabled" => false,
                "data" => []
            ]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return "pn_bundle_formbundle_sectionattribute_eav";
    }

}
