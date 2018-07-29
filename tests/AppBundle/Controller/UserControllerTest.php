<?php
namespace  Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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

    public function testUserListPageIsUp()
    {
        $crawler = $this->client->request('GET', '/users', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));
        $this->assertEquals(1, $crawler->filter('html:contains("Nom d\'utilisateur")')->count());
    }

    public function testUsersAtionsAreUnreachableForAnonymousUser()
    {
        $crawler = $this->client->request('GET', '/users');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/users/create');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/users/1/edit');
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
    }

    public function testUsersAtionsAreUnreachableForNonAdminUser()
    {
        $crawler = $this->client->request('GET', '/users', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/users/create', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/users/1/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testUserCreateWithMissingFields()
    {
        $crawler = $this->client->request('GET', '/users/create', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = '';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';
        $form['user[email]'] = '';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un nom d\'utilisateur.")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir une adresse email.")')->count());
    }

    public function testUserCreateWithInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users/create', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'lalalala';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Le format de l\'adresse n\'est pas correcte.")')->count());
    }

    public function testUserCreateWithNotMatchingPassword()
    {
        $crawler = $this->client->request('GET', '/users/create', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test2';
        $form['user[email]'] = 'test@mail.com';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains(" Les deux mots de passe doivent correspondre.")')->count());
    }

    public function testUserCreateWithAlreadyUsedMail()
    {
        $crawler = $this->client->request('GET', '/users/create', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'user1@mail.com';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("This value is already used.")')->count());
    }

    public function testUserCreateWithGoodValue()
    {
        $crawler = $this->client->request('GET', '/users/create', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'test@mail.com';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("User Test")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("L\'utilisateur a bien été ajouté.")')->count());
    }

    public function testUserEditWithMissingFields()
    {
        $crawler = $this->client->request('GET', '/users/2/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = '';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';
        $form['user[email]'] = '';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un nom d\'utilisateur.")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Vous devez saisir une adresse email.")')->count());
    }

    public function testUserEditWithInvalidEmail()
    {
        $crawler = $this->client->request('GET', '/users/2/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'lalalala';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains("Le format de l\'adresse n\'est pas correcte.")')->count());
    }

    public function testUserEditWithNotMatchingPassword()
    {
        $crawler = $this->client->request('GET', '/users/2/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test2';
        $form['user[email]'] = 'test2@mail.com';

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('html:contains(" Les deux mots de passe doivent correspondre.")')->count());
    }

    public function testUserEditWithGoodValue()
    {
        $crawler = $this->client->request('GET', '/users/3/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Edited';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'test2@mail.com';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("User Edited")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("L\'utilisateur a bien été modifié")')->count());
    }
}
