<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusContactFormPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use ThreeBRS\SyliusContactFormPlugin\Model\ContactFormSettingsProviderInterface;

/** @extends AbstractType<mixed> */
class ContactFormMessageType extends AbstractType
{
    public function __construct(private ContactFormSettingsProviderInterface $contactFormSettings)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'threebrs_sylius_contact_form_plugin.email',
                'constraints' => [
                    new NotBlank(['message' => 'E-mail je povinný.']),
                    new Email(['message' => 'Zadejte platný e-mail.']),
                ],
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'threebrs_sylius_contact_form_plugin.message',
                'constraints' => [
                    new NotBlank(['message' => 'Zpráva je povinná.']),
                    new Length([
                        'min' => 5,
                        'max' => 300,
                        'minMessage' => 'Jméno musí mít alespoň {{ limit }} znaky.',
                        'maxMessage' => 'Jméno může mít maximálně {{ limit }} znaků.',])
                ],
                'required' => true,
            ]);

        if ($this->contactFormSettings->isNameRequired() !== false) {
            $builder
                ->add('customerName', TextType::class, [
                    'label' => 'threebrs_sylius_contact_form_plugin.customerName',
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 2,
                            'max' => 100,
                            'minMessage' => 'Jméno musí mít alespoň {{ limit }} znaky.',
                            'maxMessage' => 'Jméno může mít maximálně {{ limit }} znaků.',
                        ]),
                        new Regex([
                            'pattern' => '/^[\p{L} \'-]+$/u',
                            'message' => 'Jméno může obsahovat pouze písmena, mezery, pomlčky a apostrofy.',
                        ]),
                    ],
                    'required' => true,
                ]);
        } else {
            $builder
                ->add('customerName', TextType::class, [
                    'label' => 'threebrs_sylius_contact_form_plugin.customerName',
                    'constraints' => [
                        new Length([
                            'max' => 100,
                            'maxMessage' => 'Jméno může mít maximálně {{ limit }} znaků.',
                        ]),
                        new Regex([
                            'pattern' => '/^[\p{L} \'-]+$/u',
                            'message' => 'Jméno může obsahovat pouze písmena, mezery, pomlčky a apostrofy.',
                        ]),
                    ],
                    'required' => false,
                ]);
        }

        if ($this->contactFormSettings->isPhoneRequired() !== false) {
            $builder
                ->add('phone', TelType::class, [
                    'label' => 'threebrs_sylius_contact_form_plugin.phone',
                    'constraints' => [
                        new NotBlank(['message' => 'Zadejte telefonní číslo.']),
                        new Regex([
                            'pattern' => '/^\+?[0-9 ]{6,20}$/',
                            'message' => 'Zadejte platné telefonní číslo (6–15 číslic, může začínat +).',
                        ]),
                    ],
                    'required' => true,
                ]);
        } else {
            $builder
                ->add('phone', TelType::class, [
                    'label' => 'threebrs_sylius_contact_form_plugin.phone',
                    'constraints' => [
                        new NotBlank(['message' => 'Zadejte telefonní číslo.']),
                        new Regex([
                            'pattern' => '/^\+?[0-9 ]{6,20}$/',
                            'message' => 'Zadejte platné telefonní číslo (6–15 číslic, může začínat +).',
                        ]),
                    ],
                    'required' => false,
                ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'threebrs_sylius_contact_form';
    }
}
