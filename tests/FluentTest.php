<?php

use CupOfTea\Support\Fluent;
use PHPUnit\Framework\TestCase;

class SupportFluentTest extends TestCase
{
    public function testAttributesAreSetByConstructor()
    {
        $array = ['name' => 'Sven', 'age' => 24];
        $fluent = new Fluent($array);
        
        $refl = new ReflectionObject($fluent);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);
        
        $this->assertEquals($array, $attributes->getValue($fluent));
        $this->assertEquals($array, $fluent->getAttributes());
    }
    
    public function testAttributesAreSetByConstructorGivenStdClass()
    {
        $array = ['name' => 'Sven', 'age' => 24];
        $fluent = new Fluent((object) $array);
        
        $refl = new ReflectionObject($fluent);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);
        
        $this->assertEquals($array, $attributes->getValue($fluent));
        $this->assertEquals($array, $fluent->getAttributes());
    }
    
    public function testAttributesAreSetByConstructorGivenArrayable()
    {
        $array = ['name' => 'Sven', 'age' => 24];
        $fluent = new Fluent((object) $array);
        $fluent = new Fluent($fluent);
        
        $refl = new ReflectionObject($fluent);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);
        
        $this->assertEquals($array, $attributes->getValue($fluent));
        $this->assertEquals($array, $fluent->getAttributes());
    }
    
    public function testAttributesAreSetByConstructorGivenArrayIterator()
    {
        $array = ['name' => 'Sven', 'age' => 24];
        $fluent = new Fluent(new FluentArrayIteratorStub($array));
        
        $refl = new ReflectionObject($fluent);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);
        
        $this->assertEquals($array, $attributes->getValue($fluent));
        $this->assertEquals($array, $fluent->getAttributes());
    }
    
    public function testGetMethodReturnsAttribute()
    {
        $fluent = new Fluent(['name' => 'Sven']);
        
        $this->assertEquals('Sven', $fluent->get('name'));
        $this->assertEquals('Default', $fluent->get('foo', 'Default'));
        $this->assertEquals('Sven', $fluent->name);
        $this->assertNull($fluent->foo);
        
        $fluent = new Fluent(['profile' => ['name' => 'Sven']]);
        
        $this->assertEquals('Sven', $fluent->get('profile.name'));
        $this->assertNull($fluent->get('profile.name', null, false));
    }
    
    public function testSetMethodSetsAttribute()
    {
        $fluent = new Fluent();
        
        $fluent->set('name', 'Sven');
        $fluent->age = 24;
        
        $this->assertEquals('Sven', $fluent->get('name'));
        $this->assertEquals(24, $fluent->age);
        
        $fluent = new Fluent();
        
        $fluent->set('profile.age', 24);
        $fluent->set('profile.developer', true, false);
        
        $this->assertEquals(24, $fluent->profile['age']);
        $this->assertTrue($fluent->get('profile.developer'));
        $this->assertTrue($fluent->get('profile.developer', false));
    }
    
    public function testFluentIsTraversable()
    {
        $array = ['name' => 'Sven', 'age' => 24];
        $fluent = new Fluent($array);
        
        foreach ($fluent as $key => $value) {
            $iterated[$key] = $value;
        }
        
        $this->assertEquals($array, $iterated);
    }
    
    public function testMagicMethodCanBeUsedToGetAttributes()
    {
        $fluent = new Fluent();
        
        $fluent->name = 'Sven';
        
        $this->assertEquals('Sven', $fluent('name'));
        $this->assertNull($fluent('age'));
        $this->assertEquals(24, $fluent('age', 24));
    }
    
    public function testMagicMethodsCanBeUsedToSetAttributes()
    {
        $fluent = new Fluent();
        
        $fluent->name('Sven');
        $fluent->developer();
        $fluent->age(24);
        
        $this->assertEquals('Sven', $fluent->name);
        $this->assertTrue($fluent->developer);
        $this->assertEquals(24, $fluent->age);
        $this->assertInstanceOf(Fluent::class, $fluent->programmer());
    }
    
    public function testIssetUnset()
    {
        $fluent = new Fluent(['name' => 'Sven']);
        
        $this->assertTrue($fluent->has('name'));
        $this->assertTrue(isset($fluent->name));
        
        unset($fluent->name);
        
        $this->assertFalse($fluent->has('name'));
        $this->assertFalse(isset($fluent->name));
        
        $fluent = new Fluent(['profile' => ['name' => 'Sven']]);
        
        $this->assertTrue($fluent->has('profile.name'));
        $this->assertFalse($fluent->has('profile.name', false));
        
        $fluent->remove('profile.name', false);
        $this->assertTrue($fluent->has('profile.name'));
        
        $fluent->remove('profile.name');
        $this->assertFalse($fluent->has('profile.name'));
    }
    
    public function testToArrayReturnsAttribute()
    {
        $array = ['name' => 'Sven', 'age' => 24];
        $fluent = new Fluent($array);
        
        $this->assertEquals($array, $fluent->toArray());
    }
    
    public function testToJsonEncodesTheToArrayResult()
    {
        $fluent = $this->getMockBuilder(Fluent::class)
            ->setMethods(['toArray'])
            ->getMock();
        $fluent->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue('foo'));
        $results = $fluent->toJson();
        
        $this->assertJsonStringEqualsJsonString(json_encode('foo'), $results);
    }
}

class FluentArrayIteratorStub implements IteratorAggregate
{
    protected $items = [];
    
    public function __construct(array $items = [])
    {
        $this->items = (array) $items;
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
