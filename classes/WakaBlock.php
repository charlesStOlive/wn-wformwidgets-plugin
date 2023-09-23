<?php

namespace Waka\WformWidgets\Classes;


use Winter\Blocks\Classes\Block as WinterBlock;
use Cms\Classes\Controller;
use Lang;
use Winter\Storm\Exception\SystemException;

/**
 * The Block class.
 */
class WakaBlock extends WinterBlock
{
    /**
     * Renders the provided block
     */
    public static function renderTwiged(string|array $block, array $data = [], $ds = [], ?Controller $controller): string
    {
        if (!$controller) {
            $controller = new Controller();
        }

        if (is_array($block)) {
            $data = $block;
            $block = $data['_group'] ?? false;
        }

        if (empty($block)) {
            throw new SystemException("The block name was not provided");
        }

        $partialData = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['_group', '_config'])) {
                continue;
            }

            $partialData[$key] = $value;
        }

        if($ds !== null) {
            $partialData = self::recursiveParseDataWithTwig($partialData, $ds);
        }


        // Allow data to be accessed via "data" key, for backwards compatibility.
        $partialData['data'] = $partialData;

        if (!empty($data['_config'])) {
            $partialData['config'] = json_decode($data['_config']);
        } else {
            $partialData['config'] = static::getDefaultConfig($block);
        }

        return $controller->renderPartial($block . '.block', $partialData);
    }

    /**
     * Renders the provided blocks
     */
    public static function renderTwigedAll(array $blocks, $ds = [], ?Controller $controller): string
    {
        $content = '';
        $controller ??= (new Controller());

        foreach ($blocks as $i => $block) {
            if (!array_key_exists('_group', $block)) {
                throw new SystemException("The block definition at index $i must contain a `_group` key.");
            }

            $partialData = [];

            foreach ($block as $key => $value) {
                if (in_array($key, ['_group', '_config'])) {
                    continue;
                }

                $partialData[$key] = $value;
            }

            // Allow data to be accessed via "data" key, for backwards compatibility.
            $partialData['data'] = $partialData;

            if (!empty($block['_config'])) {
                $partialData['config'] = json_decode($block['_config']);
            } else {
                $partialData['config'] = static::getDefaultConfig($block['_group']);
            }

            $content .= $controller->renderPartial($block['_group'] . '.block', $partialData);
        }

        return $content;
    }

    private static function  recursiveParseDataWithTwig(array $array, $ds = []): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::recursiveParseDataWithTwig($value, $ds);
            } elseif (is_string($value)) {
                $result[$key] = \Twig::parse($value, $ds);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
