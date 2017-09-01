<?php
declare(strict_types=1);

namespace Roave\BetterReflectionTest\Reflection\Mutation;

use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\Exception\NotAClassReflection;
use Roave\BetterReflection\Reflection\Mutation\SetClassFinal;
use Roave\BetterReflection\Reflection\Mutator\ReflectionClassMutator;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;
use Roave\BetterReflectionTest\Reflection\Mutator\ReflectionMutatorsSingleton;

/**
 * @covers \Roave\BetterReflection\Reflection\Mutation\SetClassFinal
 */
class SetClassFinalTest extends TestCase
{
    /**
     * @var Locator
     */
    private $astLocator;

    /**
     * @var ReflectionClassMutator
     */
    private $classMutator;

    protected function setUp() : void
    {
        parent::setUp();

        $this->astLocator   = (new BetterReflection())->astLocator();
        $this->classMutator = ReflectionMutatorsSingleton::instance()->classMutator();
    }

    public function testInvalidClass() : void
    {
        $php = '<?php interface Foo {}';

        $classReflection = (new ClassReflector(new StringSourceLocator($php, $this->astLocator)))->reflect('Foo');

        $this->expectException(NotAClassReflection::class);
        (new SetClassFinal($this->classMutator))->__invoke($classReflection, true);
    }

    public function testValidClass() : void
    {
        $php = '<?php final class Foo {}';

        $classReflection = (new ClassReflector(new StringSourceLocator($php, $this->astLocator)))->reflect('Foo');

        $classReflectionModifiedToFinal = (new SetClassFinal($this->classMutator))->__invoke($classReflection, true);

        self::assertNotSame($classReflection, $classReflectionModifiedToFinal);
        self::assertTrue($classReflectionModifiedToFinal->isFinal());

        $classReflectionModifiedToNotFinal = (new SetClassFinal($this->classMutator))->__invoke($classReflectionModifiedToFinal, false);

        self::assertNotSame($classReflectionModifiedToFinal, $classReflectionModifiedToNotFinal);
        self::assertFalse($classReflectionModifiedToNotFinal->isFinal());
    }
}
