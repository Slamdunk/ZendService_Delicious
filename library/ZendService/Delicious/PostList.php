<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendService\Delicious;

/**
 * List of posts retrived from the del.icio.us web service
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Delicious
 */
class PostList implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var array Array of Zend_Service_Delicious_Post
     */
    protected $posts = array();

    /**
     * @var Zend_Service_Delicious Service that has downloaded the post list
     */
    protected $service;

    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;

    /**
     * @param  Zend_Service_Delicious $service Service that has downloaded the post
     * @param  DOMNodeList|array      $posts
     * @return void
     */
    public function __construct(Delicious $service, $posts = null)
    {
        $this->service = $service;
        if ($posts instanceof \DOMNodeList) {
            $this->constructFromNodeList($posts);
        } elseif (is_array($posts)) {
            $this->constructFromArray($posts);
        }
    }

    /**
     * Transforms DOMNodeList to array of posts
     *
     * @param  DOMNodeList $nodeList
     * @return void
     */
    private function constructFromNodeList(\DOMNodeList $nodeList)
    {
        for ($i = 0; $i < $nodeList->length; $i++) {
            $curentNode = $nodeList->item($i);
            if($curentNode->nodeName == 'post') {
                $this->addPost(new Post($this->service, $curentNode));
            }
        }
    }

    /**
     * Transforms the Array to array of posts
     *
     * @param  array $postList
     * @return void
     */
    private function constructFromArray(array $postList)
    {
        foreach ($postList as $f_post) {
            $this->addPost(new SimplePost($f_post));
        }
    }

    /**
     * Add a post
     *
     * @param  Zend_Service_Delicious_SimplePost $post
     * @return Zend_Service_Delicious_PostList
     */
    protected function addPost(SimplePost $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Filter list by list of tags
     *
     * @param  array $tags
     * @return Zend_Service_Delicious_PostList
     */
    public function withTags(array $tags)
    {
        $postList = new self($this->service);

        foreach ($this->posts as $post) {
            if (count(array_diff($tags, $post->getTags())) == 0) {
                $postList->addPost($post);
            }
        }

        return $postList;
    }

    /**
     * Filter list by tag
     *
     * @param  string $tag
     * @return Zend_Service_Delicious_PostList
     */
    public function withTag($tag)
    {
        return $this->withTags(func_get_args());
    }

    /**
     * Filter list by urls matching a regular expression
     *
     * @param  string $regexp
     * @return Zend_Service_Delicious_PostList
     */
    public function withUrl($regexp)
    {
        $postList = new self($this->service);

        foreach ($this->posts as $post) {
            if (preg_match($regexp, $post->getUrl())) {
                $postList->addPost($post);
            }
        }

        return $postList;
    }

    /**
     * Return number of posts
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->posts);
    }

    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Zend_Service_Delicious_SimplePost
     */
    public function current()
    {
        return $this->posts[$this->iteratorKey];
    }

    /**
     * Return the key of the current element
     *
     * Implement Iterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->iteratorKey;
    }

    /**
     * Move forward to next element
     *
     * Implement Iterator::next()
     *
     * @return void
     */
    public function next()
    {
        $this->iteratorKey += 1;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Implement Iterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        $this->iteratorKey = 0;
    }

    /**
     * Check if there is a current element after calls to rewind() or next()
     *
     * Implement Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        $numItems = $this->count();

        if ($numItems > 0 && $this->iteratorKey < $numItems) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param   int     $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return ($offset < $this->count());
    }

    /**
     * Return value at given offset
     *
     * Implement ArrayAccess::offsetGet()
     *
     * @param   int     $offset
     * @throws  OutOfBoundsException
     * @return  Zend_Service_Delicious_SimplePost
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->posts[$offset];
        } else {
            throw new \OutOfBoundsException('Illegal index');
        }
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Zend_Service_Delicious_Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  Zend_Service_Delicious_Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception('You are trying to unset read-only property');
    }
}
