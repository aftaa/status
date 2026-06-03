<?php

namespace App\Form;

use App\Dto\TaskDto;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('status', EnumType::class, ['class' => TaskStatus::class])
//            ->add('is_completed', CheckboxType::class, [
//                'label_attr' => ['class' => 'checkbox-switch'],
//                'required' => false,
//            ])
//            ->add('created_at', null, [
//                'widget' => 'single_text',
//            ])
            ->add('name', TextType::class, [
                'label' => 'Название задачи',
                'attr' => ['placeholder' => 'Введите название'],
            ])
            ->add('status', EnumType::class, [
                'class' => TaskStatus::class,
                'label' => 'Статус',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Task::class,
            'data_class' => TaskDto::class,
        ]);
    }
}
