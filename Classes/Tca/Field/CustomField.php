<?php

namespace Nemo64\Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This type can be used if the correct configuration isn't (correctly) implemented.
 * Example:
 *
 * ->configure(new \Nemo64\Typo3Api\Tca\Field\CustomField('favourite_color', [
 *     'dbType' => "VARCHAR(7) DEFAULT '#000000' NOT NULL",
 *     'localize' => false,
 *     'config' => [
 *         'type' => 'input',
 *         'renderType' => 'colorpicker',
 *         'size' => 7,
 *         'default' => '#000000'
 *     ]
 * ]))
 */
class CustomField extends AbstractField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('config');
        $resolver->setAllowedTypes('config', 'array');
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return $this->getOption('config');
    }
}