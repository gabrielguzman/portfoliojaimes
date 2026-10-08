import { test } from 'node:test';
import { strictEqual } from 'node:assert';
import { imageSwipeOffset } from '../../resources/js/imageGestures.ts';

test('swiping left advances to the next image', () => {
 strictEqual(imageSwipeOffset({ x: 250, y: 200 }, { x: 100, y: 210 }), 1);
});

test('swiping right returns to the previous image', () => {
 strictEqual(imageSwipeOffset({ x: 100, y: 200 }, { x: 250, y: 210 }), -1);
});

test('vertical scrolling does not change the image', () => {
 strictEqual(imageSwipeOffset({ x: 100, y: 200 }, { x: 170, y: 400 }), 0);
});

test('short accidental drags do not change the image', () => {
 strictEqual(imageSwipeOffset({ x: 100, y: 200 }, { x: 145, y: 200 }), 0);
});

test('diagonal scrolling does not change the image', () => {
 strictEqual(imageSwipeOffset({ x: 100, y: 200 }, { x: 210, y: 300 }), 0);
});
