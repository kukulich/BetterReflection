<?php
declare(strict_types=1);

namespace Roave\BetterReflectionTest\Reflection\Mutation;

use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\Mutation\RemoveClassProperty;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;

/**
 * @covers \Roave\BetterReflection\Reflection\Mutation\RemoveClassProperty
 */
class RemoveClassPropertyTest extends TestCase
{
    /**
     * @var Locator
     */
    private $astLocator;

    protected function setUp() : void
    {
        parent::setUp();

        $this->astLocator = (new BetterReflection())->astLocator();
    }

    public function testRemove() : void
    {
        $php = <<<'PHP'
<?php
class Foo
{
    public $foo;
}
PHP;

        $reflector       = new ClassReflector(new StringSourceLocator($php, $this->astLocator));
        $classReflection = $reflector->reflect('Foo');

        self::assertTrue($classReflection->hasProperty('foo'));

        $modifiedClassReflection = (new RemoveClassProperty())->__invoke($classReflection, 'foo');

        self::assertInstanceOf(ReflectionClass::class, $modifiedClassReflection);
        self::assertNotSame($classReflection, $modifiedClassReflection);
        self::assertLessThan(\count($classReflection->getProperties()), \count($modifiedClassReflection->getProperties()));
        self::assertCount(0, $modifiedClassReflection->getProperties());
        self::assertFalse($modifiedClassReflection->hasProperty('foo'));
        self::assertNull($modifiedClassReflection->getProperty('foo'));
    }
}