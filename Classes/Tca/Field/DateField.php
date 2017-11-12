<?php

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateField extends AbstractField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => 'date',

            // in my tests, these 3 didn't work at all
//            'default' => null,
//            'min' => null,
//            'max' => null,

            /**
             * Typo3 allows to use a date or datetime field in the database.
             * However, it is largely untested, I'd advise not to use it.
             * At least not until some core table actually uses it so at least someone has tested it.
             *
             * @see \TYPO3\CMS\Core\DataHandling\DataHandler::checkValueForInput
             */
            'useDateTime' => false,

            'dbType' => function (Options $options) {
                switch ($options['useDateTime'] ? $options['type'] : null) {
                    case 'date':
                        return "DATE DEFAULT NULL";
                    case 'datetime':
                        return "DATETIME DEFAULT NULL";
                    default:
                        return "INT(11) DEFAULT NULL";
                }
            },
            'exposedDbType' => function (Options $options) {
                switch ($options['useDateTime'] ? $options['type'] : null) {
                    case 'date':
                        return "date";
                    case 'datetime':
                        return "datetime";
                    default:
                        return null;
                }
            },
            'localize' => false,
        ]);

        $resolver->setAllowedValues('type', ['date', 'datetime', 'time', 'timesec']);
        $resolver->setAllowedTypes('useDateTime', 'bool');

//        $timestampDate = function (Options $options, $value) {
//            if (is_null($value)) {
//                return null;
//            }
//
//            if (is_int($value)) {
//                return $value;
//            }
//
//            if (is_string($value)) {
//                return strtotime($value);
//            }
//
//            if ($value instanceof \DateTimeInterface) {
//                return $value->getTimestamp();
//            }
//
//            $type = is_object($value) ? get_class($value) : gettype($value);
//            throw new InvalidOptionsException("Don't know how to convert a date of type $type to a timestamp.");
//        };
//        $resolver->setNormalizer('min', $timestampDate);
//        $resolver->setNormalizer('max', $timestampDate);
//        $resolver->setNormalizer('default', $timestampDate);
    }

    public function getFieldTcaConfig(string $tableName)
    {
        $config = [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'dbType' => $this->getOption('exposedDbType'),
            'eval' => $this->getOption('type'),
            'range' => [],
        ];

//        if ($this->getOption('default') !== null) {
//            $config['default'] = $this->getOption('default');
//        }
//
//        if ($this->getOption('max') !== null) {
//            $config['range']['upper'] = $this->getOption('max');
//        }
//
//        if ($this->getOption('min') !== null) {
//            $config['range']['lower'] = $this->getOption('min');
//        }

        return $config;
    }
}