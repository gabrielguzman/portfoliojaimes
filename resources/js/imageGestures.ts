export type TouchPoint = { x: number; y: number };

export function imageSwipeOffset(start: TouchPoint, end: TouchPoint): -1 | 0 | 1 {
 const horizontal = end.x - start.x;
 const vertical = end.y - start.y;

 if (Math.abs(horizontal) <= 60 || Math.abs(horizontal) <= Math.abs(vertical) * 1.5) {
  return 0;
 }

 return horizontal < 0 ? 1 : -1;
}
