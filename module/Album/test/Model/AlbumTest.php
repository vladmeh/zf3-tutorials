<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2016, Alpha-Hydro
 *
 */

namespace AlbumTest\Model;

use Album\Model\Album;
use PHPUnit_Framework_TestCase as TestCase;

class AlbumTest extends TestCase
{
    /**
     * Действительно ли все Album'ы свойств изначально настроены на NULL
     */
    public function testInitialAlbumValuesAreNull()
    {
        $album = new Album();

        $this->assertNull($album->artist, '"artist" should be null by default');
        $this->assertNull($album->id, '"id" should be null by default');
        $this->assertNull($album->title, '"title" should be null by default');
    }

    /**
     * Будут ли Album's свойства установлены правильно,
     * когда мы вызываем exchangeArray()
     */
    public function testExchangeArraySetsPropertiesCorrectly()
    {
        $album = new Album();
        $data  = [
            'artist' => 'some artist',
            'id'     => 123,
            'title'  => 'some title'
        ];

        $album->exchangeArray($data);

        $this->assertSame(
            $data['artist'],
            $album->artist,
            '"artist" was not set correctly'
        );

        $this->assertSame(
            $data['id'],
            $album->id,
            '"id" was not set correctly'
        );

        $this->assertSame(
            $data['title'],
            $album->title,
            '"title" was not set correctly'
        );
    }

    /**
     * Будет ли использоваться значение по умолчанию NULL для свойств,
     * чьи ключи не присутствуют в $data массиве?
     */
    public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent()
    {
        $album = new Album();

        $album->exchangeArray([
            'artist' => 'some artist',
            'id'     => 123,
            'title'  => 'some title',
        ]);
        $album->exchangeArray([]);

        $this->assertNull($album->artist, '"artist" should default to null');
        $this->assertNull($album->id, '"id" should default to null');
        $this->assertNull($album->title, '"title" should default to null');
    }

    /**
     * Можем ли мы получить копию массива нашей модели?
     */
    public function testGetArrayCopyReturnsAnArrayWithPropertyValues()
    {
        $album = new Album();
        $data  = [
            'artist' => 'some artist',
            'id'     => 123,
            'title'  => 'some title'
        ];

        $album->exchangeArray($data);
        $copyArray = $album->getArrayCopy();

        $this->assertSame($data['artist'], $copyArray['artist'], '"artist" was not set correctly');
        $this->assertSame($data['id'], $copyArray['id'], '"id" was not set correctly');
        $this->assertSame($data['title'], $copyArray['title'], '"title" was not set correctly');
    }

    /**
     * Все ли элементы присутствуют во входных фильтрах?
     */
    public function testInputFiltersAreSetCorrectly()
    {
        $album = new Album();

        $inputFilter = $album->getInputFilter();

        $this->assertSame(3, $inputFilter->count());
        $this->assertTrue($inputFilter->has('artist'));
        $this->assertTrue($inputFilter->has('id'));
        $this->assertTrue($inputFilter->has('title'));
    }
}