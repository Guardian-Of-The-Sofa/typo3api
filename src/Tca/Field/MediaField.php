<?php

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MediaField extends FileField
{
    /**
     * I blacklist some types which aren't well supported
     * This time it's browser support which is lacking
     * all the audio formats you see basically shouldn't be used
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Supported_media_formats#Browser_compatibility
     *
     * @see \Typo3Api\Tca\Field\ImageField::BLACKLISTED_FORMATS
     */
    const BLACKLISTED_FORMATS = ['wav', 'ogg', 'flac', 'opus', 'webm'];

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'allowedFileExtensions' => array_diff(
                GeneralUtility::trimExplode(',', strtolower($GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'])),
                ImageField::BLACKLISTED_FORMATS,
                MediaField::BLACKLISTED_FORMATS
            ),
        ]);
    }

    public function getFieldTcaConfig(string $tableName)
    {
        $config = parent::getFieldTcaConfig($tableName);

        // copy the column overrides from the image type in tt_content
        // i don't want to copy paste all that definition stuff
        $config['overrideChildTca']['types'] = $GLOBALS['TCA']['tt_content']['columns']['assets']['config']['overrideChildTca']['types'];
        $config['appearance'] = array_merge(
            $GLOBALS['TCA']['tt_content']['columns']['assets']['config']['appearance'],
            $config['appearance']
        );

        return $config;
    }
}