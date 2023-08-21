**Connect a Piece 1.0 βeta**

*Creator: John Pepp*

*Creation Date: August 16, 2023*

*Last Updated: August 20, 2023*

**Game Description:**

The game is a digital jigsaw puzzle without the jigsaw. Here are the primary features and mechanics of the game:

1. **Initialization**: The game sets up a canvas and audio assets to work within a web environment. Sound effects include a snap sound for when puzzle pieces fit together and a celebration sound for when the puzzle is solved.

2. **Loading Puzzles**: The game loads a jigsaw puzzle image fetched from a server-side PHP script (`fetch_image.php`). Alongside each puzzle image, a textual description is also fetched.

3. **Gameplay**:
    - The puzzle image is divided into a predefined number of pieces, determined by the constant `PIECE_COUNT`.
    - These pieces are scattered randomly across the canvas, making sure they don't overlap.
    - Players drag pieces around the canvas. If a piece is dragged close to its correct position, it snaps into place, accompanied by a sound effect.
    - Once all pieces are correctly placed, a celebration sound plays, and the player is congratulated. The next puzzle is then loaded.

4. **User Interface**:
    - An alert system is in place to notify players with messages. For example, when they've completed all available puzzles.
    - The game cursor changes when hovering over a puzzle piece, indicating that it can be dragged.

5. **Error Handling**:
    - If there's an error loading an image, it's logged to the console.
    - If a non-overlapping position for a puzzle piece isn't found after a certain number of attempts (defined by `MAX_ATTEMPTS`), a warning is issued.

6. **Helper Functions**:
    - The game contains multiple helper functions to facilitate various tasks such as redrawing the canvas, checking if two puzzle pieces overlap, setting up the canvas background, and checking for puzzle completion.

**Objective**: Players must piece together the puzzle by dragging and fitting pieces into their correct positions. Once all pieces fit together, the puzzle is deemed solved, and the next one is presented.

**Endgame**: After completing all puzzles, players are notified that they've completed all available puzzles.
