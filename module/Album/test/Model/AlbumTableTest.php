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
use Album\Model\AlbumTable;

use PHPUnit_Framework_TestCase as TestCase;

use Zend\Db\ResultSet\ResultSetInterface;
use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * @property \Prophecy\Prophecy\ObjectProphecy tableGateway
 * @property AlbumTable albumTable
 */
class AlbumTableTest extends TestCase
{

    protected function setUp()
    {
        $this->tableGateway = $this->prophesize(TableGatewayInterface::class);
        $this->albumTable = new AlbumTable($this->tableGateway->reveal());
    }

    /**
     * Мы можем получить все альбомы.
     */
    public function testFetchAllReturnsAllAlbums()
    {
        $resultSet = $this->prophesize(ResultSetInterface::class)->reveal();
        $this->tableGateway->select()->willReturn($resultSet);

        $this->assertSame($resultSet, $this->albumTable->fetchAll());
    }

    /**
     * Мы можем удалить альбом его ID.
     */
    public function testCanDeleteAnAlbumByItsId()
    {
        $this->tableGateway->delete(['id' => 123])->shouldBeCalled();
        $this->albumTable->deleteAlbum(123);
    }

    /**
     * Мы можем сохранить новый альбом.
     */
    public function testSaveAlbumWillInsertNewAlbumsIfTheyDontAlreadyHaveAnId()
    {
        $albumData = [
            'artist' => 'The Military Wives',
            'title'  => 'In My Dreams'
        ];
        $album = new Album();
        $album->exchangeArray($albumData);

        $this->tableGateway->insert($albumData)->shouldBeCalled();
        $this->albumTable->saveAlbum($album);
    }

    /**
     * Мы можем обновить существующие альбомы.
     */
    public function testSaveAlbumWillUpdateExistingAlbumsIfTheyAlreadyHaveAnId()
    {
        $albumData = [
            'id'     => 123,
            'artist' => 'The Military Wives',
            'title'  => 'In My Dreams',
        ];
        $album = new Album();
        $album->exchangeArray($albumData);

        $resultSet = $this->prophesize(ResultSetInterface::class);
        $resultSet->current()->willReturn($album);

        $this->tableGateway
            ->select(['id' => 123])
            ->willReturn($resultSet->reveal());
        $this->tableGateway
            ->update(
                array_filter($albumData, function ($key) {
                    return in_array($key, ['artist', 'title']);
                }, ARRAY_FILTER_USE_KEY),
                ['id' => 123]
            )->shouldBeCalled();

        $this->albumTable->saveAlbum($album);
    }

    /**
     * Мы будем получать исключение,
     * если мы пытаемся извлечь альбом,
     * который не существует.
     */
    public function testExceptionIsThrownWhenGettingNonExistentAlbum()
    {
        $resultSet = $this->prophesize(ResultSetInterface::class);
        $resultSet->current()->willReturn(null);

        $this->tableGateway
            ->select(['id' => 123])
            ->willReturn($resultSet->reveal());

        $this->setExpectedException(
            RuntimeException::class,
            'Could not find row with identifier 123'
        );
        $this->albumTable->getAlbum(123);
    }
}