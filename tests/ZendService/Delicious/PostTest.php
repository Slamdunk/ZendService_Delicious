<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendServiceTest\Delicious;

use ZendService\Delicious\Delicious as DeliciousClient;
use ZendService\Delicious;
use ZendService\Delicious\Post;

/**
 * @category   Zend_Service
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Delicious
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    const UNAME = 'zfTestUser';
    const PASS  = 'zfuser';

    /**
     * Service consumer object
     *
     * @var Zend_Service_Delicious
     */
    protected $_delicious;

    /**
     * Post object
     *
     * @var Zend_Service_Delicious_Post
     */
    protected $_post;

    /**
     * Creates an instance of Zend_Service_Delicious for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_delicious = new DeliciousClient(self::UNAME, self::PASS);

        $values = array(
            'title' => 'anything',
            'url'   => 'anything'
            );
        $this->_post = new Post($this->_delicious, $values);
    }

    /**
     * Ensures that the constructor throws an exception when the title is missing from the values
     *
     * @return void
     */
    public function testConstructExceptionValuesTitleMissing()
    {
        try {
            $post = new Post($this->_delicious, array('url' => 'anything'));
            $this->fail('Expected \ZendService\Delicious\Exception not thrown');
        } catch (Delicious\Exception $e) {
            $this->assertContains("'url' and 'title'", $e->getMessage());
        }
    }

    /**
     * Ensures that the constructor throws an exception when the URL is missing from the values
     *
     * @return void
     */
    public function testConstructExceptionValuesUrlMissing()
    {
        try {
            $post = new Post($this->_delicious, array('title' => 'anything'));
            $this->fail('Expected \ZendService\Delicious\Exception not thrown');
        } catch (Delicious\Exception $e) {
            $this->assertContains("'url' and 'title'", $e->getMessage());
        }
    }

    /**
     * Ensures that the constructor throws an exception when the date value is
     * not an instance of DateTime
     *
     * @return void
     */
    public function testConstructExceptionValuesDateInvalid()
    {
        $values = array(
            'title' => 'anything',
            'url'   => 'anything',
            'date'  => 'invalid'
            );
        $this->setExpectedException('ZendService\Delicious\Exception',
                                    'instance of DateTime');
        new Post($this->_delicious, $values);
    }

    /**
     * Ensures that setTitle() provides a fluent interface
     *
     * @return void
     */
    public function testSetTitleFluent()
    {
        $this->assertSame($this->_post, $this->_post->setTitle('something'));
    }

    /**
     * Ensures that setNotes() provides a fluent interface
     *
     * @return void
     */
    public function testSetNotesFluent()
    {
        $this->assertSame($this->_post, $this->_post->setNotes('something'));
    }

    /**
     * Ensures that setTags() provides a fluent interface
     *
     * @return void
     */
    public function testSetTagsFluent()
    {
        $this->assertSame($this->_post, $this->_post->setTags(array('something')));
    }

    /**
     * Ensures that addTag() provides a fluent interface
     *
     * @return void
     */
    public function testAddTagFluent()
    {
        $this->assertSame($this->_post, $this->_post->addTag('another'));
    }

    /**
     * Ensures that removeTag() provides a fluent interface
     *
     * @return void
     */
    public function testRemoveTagFluent()
    {
        $this->assertSame($this->_post, $this->_post->removeTag('missing'));
    }

    /**
     * Ensures that getDate() provides expected behavior
     *
     * @return void
     */
    public function testGetDate()
    {
        $this->assertNull($this->_post->getDate());
    }

    /**
     * Ensures that getOthers() provides expected behavior
     *
     * @return void
     */
    public function testGetOthers()
    {
        $this->assertNull($this->_post->getOthers());
    }

    /**
     * Ensures that getHash() provides expected behavior
     *
     * @return void
     */
    public function testGetHash()
    {
        $this->assertNull($this->_post->getHash());
    }

    /**
     * Ensures that getShared() provides expected behavior
     *
     * @return void
     */
    public function testGetShared()
    {
        $this->assertTrue($this->_post->getShared());
    }

    /**
     * Ensures that setShared() provides a fluent interface
     *
     * @return void
     */
    public function testSetSharedFluent()
    {
        $this->assertSame($this->_post, $this->_post->setShared(true));
    }
}
