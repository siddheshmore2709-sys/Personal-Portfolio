(() => {
  const root = document.documentElement;
  const savedTheme = localStorage.getItem("portfolio-theme");
  const preferredTheme = window.matchMedia("(prefers-color-scheme: light)")
    .matches
    ? "light"
    : "dark";

  root.setAttribute("data-bs-theme", savedTheme || preferredTheme);

  const dotGrid = document.createElement("canvas");
  dotGrid.className = "dot-grid";
  dotGrid.setAttribute("aria-hidden", "true");
  dotGrid.setAttribute("data-dot-grid", "true");
  document.body.prepend(dotGrid);
  const dotContext = dotGrid?.getContext("2d");
  const supportsPointer = window.matchMedia("(pointer: fine)").matches;

  if (dotGrid && dotContext) {
    const dotSpacing = 30;
    const interactionRadius = 180;
    const maximumDisplacement = 15;
    const recoverySpeed = 0.16;
    const movementSpeed = 0.22;
    const dots = [];
    const pointer = { x: 0, y: 0, active: false };
    let animationFrame = 0;
    let releaseTimer;
    let canvasWidth = 0;
    let canvasHeight = 0;
    let devicePixelRatio = 1;

    const resizeDotGrid = () => {
      canvasWidth = window.innerWidth;
      canvasHeight = window.innerHeight;
      devicePixelRatio = Math.min(window.devicePixelRatio || 1, 2);
      dotGrid.width = Math.floor(canvasWidth * devicePixelRatio);
      dotGrid.height = Math.floor(canvasHeight * devicePixelRatio);
      dotGrid.style.width = `${canvasWidth}px`;
      dotGrid.style.height = `${canvasHeight}px`;
      dotContext.setTransform(devicePixelRatio, 0, 0, devicePixelRatio, 0, 0);
      dots.length = 0;

      for (let y = -dotSpacing; y <= canvasHeight + dotSpacing; y += dotSpacing) {
        for (let x = -dotSpacing; x <= canvasWidth + dotSpacing; x += dotSpacing) {
          dots.push({ x, y, offsetX: 0, offsetY: 0 });
        }
      }

      drawDotGrid();
    };

    const getDotColor = () =>
      getComputedStyle(document.documentElement)
        .getPropertyValue("--dot-color")
        .trim();

    const drawDotGrid = () => {
      dotContext.clearRect(0, 0, canvasWidth, canvasHeight);
      dotContext.fillStyle = getDotColor();

      for (const dot of dots) {
        dotContext.beginPath();
        dotContext.arc(dot.x + dot.offsetX, dot.y + dot.offsetY, 1.15, 0, Math.PI * 2);
        dotContext.fill();
      }
    };

    const settleDots = () => {
      let needsAnotherFrame = false;

      for (const dot of dots) {
        dot.offsetX += (0 - dot.offsetX) * recoverySpeed;
        dot.offsetY += (0 - dot.offsetY) * recoverySpeed;
        if (Math.abs(dot.offsetX) > 0.05 || Math.abs(dot.offsetY) > 0.05) {
          needsAnotherFrame = true;
        } else {
          dot.offsetX = 0;
          dot.offsetY = 0;
        }
      }

      drawDotGrid();
      if (needsAnotherFrame) {
        animationFrame = requestAnimationFrame(settleDots);
      } else {
        animationFrame = 0;
      }
    };

    const moveDots = () => {
      let needsAnotherFrame = false;

      for (const dot of dots) {
        const distanceX = dot.x - pointer.x;
        const distanceY = dot.y - pointer.y;
        const distance = Math.hypot(distanceX, distanceY);
        const influence = Math.max(0, 1 - distance / interactionRadius);
        const strength = influence * influence;
        const targetX = distance === 0 ? 0 : (distanceX / distance) * strength * maximumDisplacement;
        const targetY = distance === 0 ? 0 : (distanceY / distance) * strength * maximumDisplacement;

        dot.offsetX += (targetX - dot.offsetX) * movementSpeed;
        dot.offsetY += (targetY - dot.offsetY) * movementSpeed;
        if (Math.abs(dot.offsetX - targetX) > 0.05 || Math.abs(dot.offsetY - targetY) > 0.05) {
          needsAnotherFrame = true;
        }
      }

      drawDotGrid();
      if (pointer.active || needsAnotherFrame) {
        animationFrame = requestAnimationFrame(pointer.active ? moveDots : settleDots);
      } else {
        animationFrame = 0;
      }
    };

    const startDotAnimation = () => {
      if (!animationFrame) {
        animationFrame = requestAnimationFrame(pointer.active ? moveDots : settleDots);
      }
    };

    const releasePointer = () => {
      pointer.active = false;
      startDotAnimation();
    };

    const handlePointerMove = (event) => {
      pointer.x = event.clientX;
      pointer.y = event.clientY;
      pointer.active = true;
      window.clearTimeout(releaseTimer);
      releaseTimer = window.setTimeout(releasePointer, 90);
      startDotAnimation();
    };

    resizeDotGrid();
    window.addEventListener("resize", resizeDotGrid, { passive: true });

    if (supportsPointer) {
      window.addEventListener("pointermove", handlePointerMove, { passive: true });
      window.addEventListener("pointerleave", releasePointer, { passive: true });
      window.addEventListener("blur", releasePointer, { passive: true });
    }

    new MutationObserver(drawDotGrid).observe(document.documentElement, {
      attributes: true,
      attributeFilter: ["data-bs-theme"],
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.querySelector("[data-theme-toggle]");
    if (!toggle) return;

    const updateToggle = () => {
      const isLight = root.getAttribute("data-bs-theme") === "light";
      const label = isLight ? "Switch to dark theme" : "Switch to light theme";
      toggle.textContent = isLight ? "☾" : "☀";
      toggle.setAttribute("aria-label", label);
      toggle.setAttribute("title", label);
      toggle.setAttribute("aria-pressed", String(isLight));
    };

    toggle.addEventListener("click", () => {
      const nextTheme =
        root.getAttribute("data-bs-theme") === "light" ? "dark" : "light";
      root.setAttribute("data-bs-theme", nextTheme);
      localStorage.setItem("portfolio-theme", nextTheme);
      updateToggle();
    });

    updateToggle();
  });
})();
