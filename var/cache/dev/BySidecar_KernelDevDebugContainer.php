<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerFIR23Xx\BySidecar_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerFIR23Xx/BySidecar_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerFIR23Xx.legacy');

    return;
}

if (!\class_exists(BySidecar_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerFIR23Xx\BySidecar_KernelDevDebugContainer::class, BySidecar_KernelDevDebugContainer::class, false);
}

return new \ContainerFIR23Xx\BySidecar_KernelDevDebugContainer([
    'container.build_hash' => 'FIR23Xx',
    'container.build_id' => '52176ed2',
    'container.build_time' => 1590155682,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerFIR23Xx');