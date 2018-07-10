<?php
namespace  Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function tearDown()
    {
        $this->client = null;
    }

    public function testTaskListPageIsUp()
    {
        $crawler = $this->client->request('GET', '/tasks', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));
        $this->assertEquals(1, $crawler->filter('a[href="/tasks/create"]')->count());
    }

    public function testTaskAtionsAreUnreachableForAnonymousUser()
    {
        $crawler = $this->client->request('GET', '/tasks');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/1/toggle');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/create');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/1/delete');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
    }

    public function testTaskCreateWithMissingFields()
    {
        $crawler = $this->client->request('GET', '/tasks/create', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = '';
        $form['task[content]'] = '';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un titre.")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir du contenu.")')->count());
    }

    public function testTaskCreateWithGoodValues()
    {
        $crawler = $this->client->request('GET', '/tasks/create', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = 'test test';
        $form['task[content]'] = 'test test';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("La tâche a été bien été ajoutée.")')->count());
    }

    public function testTaskEditWithMissingFields()
    {
        $crawler = $this->client->request('GET', '/tasks/2/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = '';
        $form['task[content]'] = '';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un titre.")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir du contenu.")')->count());
    }

    public function testTaskEditWithGoodValues()
    {
        $crawler = $this->client->request('GET', '/tasks/2/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = 'test test';
        $form['task[content]'] = 'test test';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("La tâche a bien été modifiée.")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("test test")')->count());
    }

    public function testTaskToogleToIsDone()
    {
        $crawler = $this->client->request('GET', '/tasks', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));
        $form = $crawler->filter('button[class="btn btn-success btn-sm pull-right"]')->first()->form();
        $crawler = $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('html:contains("La tâche First task of admin a bien été marquée comme faite.")')->count());
    }

    public function testTaskToogleToIsNotDone()
    {
        $crawler = $this->client->request('GET', '/tasks', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));
        $form = $crawler->filter('button[class="btn btn-success btn-sm pull-right"]')->first()->form();
        $crawler = $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('html:contains("La tâche First task of admin a bien été marquée comme non terminée.")')->count());
    }

    public function testDeleteAnOwnedTask()
    {
        $crawler = $this->client->request('GET', '/tasks/1/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("First task of admin")')->count());
    }

    public function testDeleteANotOwnedTask()
    {
        $crawler = $this->client->request('GET', '/tasks/2/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testUserDeleteAnAnonymousTask()
    {
        $crawler = $this->client->request('GET', '/tasks/4/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteAnAnonymousTask()
    {
        $crawler = $this->client->request('GET', '/tasks/4/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("First task of anonymous user")')->count());
    }
}
