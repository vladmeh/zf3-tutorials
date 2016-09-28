<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2016, Alpha-Hydro
 *
 */

namespace AlbumTest\Controller;

use Album\Controller\AlbumController;
use Album\Model\Album;
use Album\Model\AlbumTable;

use Prophecy\Argument;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AlbumControllerTest extends AbstractHttpControllerTestCase
{
    protected $albumTable;
    protected $traceError = true;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
        // Grabbing the full application configuration:
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setUp();

        $this->configureServiceManager($this->getApplicationServiceLocator());
    }

    protected function configureServiceManager(ServiceManager $services)
    {
        $services->setAllowOverride(true);

        $services->setService('config', $this->updateConfig($services->get('config')));
        $services->setService(AlbumTable::class, $this->mockAlbumTable()->reveal());

        $services->setAllowOverride(false);
    }

    protected function updateConfig($config)
    {
        $config['db'] = [];
        return $config;
    }

    protected function mockAlbumTable()
    {
        $this->albumTable = $this->prophesize(AlbumTable::class);
        return $this->albumTable;
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->albumTable->fetchAll()->willReturn([]);

        $this->dispatch('/album');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName(AlbumController::class);
        $this->assertControllerClass('AlbumController');
        $this->assertMatchedRouteName('album');
    }

    public function testAddActionRedirectsAfterValidPost()
    {
        $this->albumTable
            ->saveAlbum(Argument::type(Album::class))
            ->shouldBeCalled();

        $postData = [
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '',
        ];
        $this->dispatch('/album/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

    public function testAddActionRedirectsAfterValidGet()
    {
        $postData = [
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '',
        ];
        $this->dispatch('/album/add', 'GET', $postData);
        $this->assertResponseStatusCode(200);
    }

    public function testEditActionRedirectAfterValidPost()
    {
        $id = '125';
        $this->albumTable->getAlbum($id)->willReturn(new Album());
        $this->albumTable
            ->saveAlbum(Argument::type(Album::class))
            ->shouldBeCalled();

        $postData = [
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zepp',
            'id'     => $id,
        ];
        $this->dispatch('/album/edit/'.$id, 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

    public function testEditActionRedirectAfterValidGet()
    {
        $id = '125';
        $this->albumTable->getAlbum($id)->willReturn(new Album());

        $postData = [
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zepp',
            'id'     => $id,
        ];
        $this->dispatch('/album/edit/'.$id, 'GET', $postData);
        $this->assertResponseStatusCode(200);
    }

    public function testDeleteActionRedirectDelYesPost()
    {
        $id = '125';
        $postData = [
            'del'  => 'Yes'
        ];

        $this->dispatch('/album/delete/'.$id, 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

    public function testDeleteActionRedirectDelNoPost()
    {
        $id = '125';
        $postData = [
            'del'  => 'No'
        ];

        $this->dispatch('/album/delete/'.$id, 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

    public function testDeleteActionRedirectDelYesGet()
    {
        $id = '125';
        $postData = [
            'del'  => 'Yes'
        ];

        $this->dispatch('/album/delete/'.$id, 'GET', $postData);
        $this->assertResponseStatusCode(200);
    }

    public function testDeleteActionIdNotInt()
    {
        $id = 'abc';

        $this->dispatch('/album/delete/'.$id);
        $this->assertResponseStatusCode(404);
    }

    public function testEditActionIdNotInt()
    {
        $id = 'abc';

        $this->dispatch('/album/edit/'.$id);
        $this->assertResponseStatusCode(404);
    }
}